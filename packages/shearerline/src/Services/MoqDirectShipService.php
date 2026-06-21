<?php

namespace Shearerline\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Shearerline\Exceptions\InsufficientStockException;
use Shearerline\Exceptions\InvalidStatusTransitionException;
use Shearerline\Exceptions\MoqNotMetException;
use Shearerline\Models\MoqOrder;
use Shearerline\Models\MoqOrderItem;
use Shearerline\Models\Product;
use Shearerline\Models\Shipment;
use Shearerline\Models\Supplier;

class MoqDirectShipService
{
    public function createOrder(array $data): MoqOrder
    {
        return DB::transaction(function () use ($data) {
            $supplier = Supplier::findOrFail($data['supplier_id']);

            $totalAmount = 0;
            $totalQuantity = 0;
            $orderItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if (!$product->meetsMoq($item['quantity'])) {
                    throw new MoqNotMetException(
                        $product->id,
                        $product->name,
                        $item['quantity'],
                        $product->moq
                    );
                }

                if (!$product->hasEnoughStock($item['quantity'])) {
                    throw new InsufficientStockException(
                        $product->id,
                        $product->name,
                        $item['quantity'],
                        $product->stock
                    );
                }

                $itemAmount = $product->price * $item['quantity'];
                $totalAmount += $itemAmount;
                $totalQuantity += $item['quantity'];

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $product->price,
                    'quantity' => $item['quantity'],
                    'shipped_quantity' => 0,
                    'subtotal' => $itemAmount,
                    'remark' => $item['remark'] ?? null,
                ];

                $product->decrement('stock', $item['quantity']);
            }

            $order = MoqOrder::create([
                'order_no' => $this->generateOrderNo(),
                'supplier_id' => $supplier->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_address' => $data['customer_address'],
                'customer_remark' => $data['customer_remark'] ?? null,
                'total_amount' => $totalAmount,
                'total_quantity' => $totalQuantity,
                'shipped_quantity' => 0,
                'created_by' => auth()->id() ?? null,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            return $this->loadOrderRelations($order);
        });
    }

    public function getOrderList(array $filters = [])
    {
        $query = MoqOrder::with('items', 'supplier', 'shipments');

        $this->applyOrderFilters($query, $filters);

        $perPage = $filters['per_page'] ?? config('shearerline.pagination.per_page', 15);

        return $query->latest()->paginate($perPage);
    }

    public function getOrderDetail(int $orderId): MoqOrder
    {
        return MoqOrder::with('items', 'supplier', 'shipments')->findOrFail($orderId);
    }

    public function confirmOrder(int $orderId): MoqOrder
    {
        return DB::transaction(function () use ($orderId) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canConfirm()) {
                throw new InvalidStatusTransitionException(
                    $order->status,
                    MoqOrder::STATUS_CONFIRMED,
                    '订单状态不允许确认'
                );
            }

            $order->transitionTo(MoqOrder::STATUS_CONFIRMED);
            $order->save();

            return $this->loadOrderRelations($order);
        });
    }

    public function processOrder(int $orderId): MoqOrder
    {
        return DB::transaction(function () use ($orderId) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canProcess()) {
                throw new InvalidStatusTransitionException(
                    $order->status,
                    MoqOrder::STATUS_PROCESSING,
                    '订单状态不允许开始处理'
                );
            }

            $order->transitionTo(MoqOrder::STATUS_PROCESSING);
            $order->save();

            return $this->loadOrderRelations($order);
        });
    }

    public function shipOrder(int $orderId, array $shipmentData): Shipment
    {
        return DB::transaction(function () use ($orderId, $shipmentData) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canShip()) {
                throw new InvalidStatusTransitionException(
                    $order->status,
                    MoqOrder::STATUS_SHIPPED,
                    '订单状态不允许发货'
                );
            }

            $shipQuantity = 0;
            $shipItems = [];

            foreach ($shipmentData['items'] as $item) {
                $orderItem = MoqOrderItem::findOrFail($item['order_item_id']);
                $remainingQuantity = $orderItem->quantity - $orderItem->shipped_quantity;

                if ($item['quantity'] > $remainingQuantity) {
                    throw new InsufficientStockException(
                        $orderItem->product_id,
                        $orderItem->product_name,
                        $item['quantity'],
                        $remainingQuantity,
                        '发货数量超过未发货数量'
                    );
                }

                $shipQuantity += $item['quantity'];
                $shipItems[] = [
                    'order_item_id' => $orderItem->id,
                    'product_name' => $orderItem->product_name,
                    'product_sku' => $orderItem->product_sku,
                    'quantity' => $item['quantity'],
                ];

                $orderItem->increment('shipped_quantity', $item['quantity']);
            }

            $newShippedQuantity = $order->shipped_quantity + $shipQuantity;
            $isFullyShipped = $newShippedQuantity >= $order->total_quantity;

            $order->shipped_quantity = $newShippedQuantity;

            if ($isFullyShipped && $order->canTransitionTo(MoqOrder::STATUS_SHIPPED)) {
                $order->transitionTo(MoqOrder::STATUS_SHIPPED);
            }

            $order->save();

            $shipment = $order->shipments()->create([
                'shipment_no' => $this->generateShipmentNo(),
                'logistics_company' => $shipmentData['logistics_company'],
                'tracking_no' => $shipmentData['tracking_no'],
                'items' => $shipItems,
                'total_quantity' => $shipQuantity,
                'weight' => $shipmentData['weight'] ?? null,
                'shipping_cost' => $shipmentData['shipping_cost'] ?? null,
                'status' => Shipment::STATUS_SHIPPED,
                'receiver_name' => $shipmentData['receiver_name'] ?? $order->customer_name,
                'receiver_phone' => $shipmentData['receiver_phone'] ?? $order->customer_phone,
                'receiver_address' => $shipmentData['receiver_address'] ?? $order->customer_address,
                'remark' => $shipmentData['remark'] ?? null,
            ]);

            return $shipment;
        });
    }

    public function updateTracking(int $shipmentId, array $trackingData): Shipment
    {
        return DB::transaction(function () use ($shipmentId, $trackingData) {
            $shipment = Shipment::findOrFail($shipmentId);

            if (!$shipment->canUpdateTracking()) {
                throw new InvalidStatusTransitionException(
                    $shipment->status,
                    $trackingData['status'] ?? 'unknown',
                    '物流状态不允许更新'
                );
            }

            $updateData = [];

            if (isset($trackingData['logistics_company'])) {
                $updateData['logistics_company'] = $trackingData['logistics_company'];
            }

            if (isset($trackingData['tracking_no'])) {
                $updateData['tracking_no'] = $trackingData['tracking_no'];
            }

            if (isset($trackingData['remark'])) {
                $updateData['remark'] = $trackingData['remark'];
            }

            if (isset($trackingData['status'])) {
                if (!$shipment->canTransitionTo($trackingData['status'])) {
                    throw new InvalidStatusTransitionException(
                        $shipment->status,
                        $trackingData['status']
                    );
                }
                $shipment->transitionTo($trackingData['status']);
            }

            if (!empty($updateData)) {
                $shipment->fill($updateData);
            }

            $shipment->save();

            $this->tryCompleteOrderFromShipment($shipment);

            return $shipment->load('order.items', 'order.supplier', 'order.shipments');
        });
    }

    protected function tryCompleteOrderFromShipment(Shipment $shipment): void
    {
        $order = $shipment->order;

        if (!$order) {
            return;
        }

        $order->refresh();
        $order->load('shipments');

        $allDelivered = $order->shipments->every(function ($s) {
            return $s->status === Shipment::STATUS_DELIVERED;
        });

        if ($allDelivered && $order->canComplete()) {
            $order->transitionTo(MoqOrder::STATUS_COMPLETED);
            $order->save();
        }
    }

    public function completeOrder(int $orderId): MoqOrder
    {
        return DB::transaction(function () use ($orderId) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canComplete()) {
                throw new InvalidStatusTransitionException(
                    $order->status,
                    MoqOrder::STATUS_COMPLETED,
                    '订单状态不允许完成'
                );
            }

            $order->transitionTo(MoqOrder::STATUS_COMPLETED);
            $order->save();

            return $this->loadOrderRelations($order);
        });
    }

    public function cancelOrder(int $orderId, string $reason): MoqOrder
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canCancel()) {
                throw new InvalidStatusTransitionException(
                    $order->status,
                    MoqOrder::STATUS_CANCELLED,
                    '订单状态不允许取消'
                );
            }

            foreach ($order->items as $item) {
                $unshippedQuantity = $item->quantity - $item->shipped_quantity;
                if ($unshippedQuantity > 0) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $unshippedQuantity);
                    }
                }
            }

            $order->cancelled_reason = $reason;
            $order->transitionTo(MoqOrder::STATUS_CANCELLED);
            $order->save();

            return $this->loadOrderRelations($order);
        });
    }

    public function refundOrder(int $orderId, string $reason): MoqOrder
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canRefund()) {
                throw new InvalidStatusTransitionException(
                    $order->status,
                    MoqOrder::STATUS_REFUNDED,
                    '订单状态不允许退款'
                );
            }

            $order->refunded_reason = $reason;
            $order->transitionTo(MoqOrder::STATUS_REFUNDED);
            $order->save();

            return $this->loadOrderRelations($order);
        });
    }

    public function payOrder(int $orderId, array $paymentData): MoqOrder
    {
        return DB::transaction(function () use ($orderId, $paymentData) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canPay()) {
                throw new InvalidStatusTransitionException(
                    $order->status,
                    'paid',
                    '订单状态不允许支付或已支付'
                );
            }

            $order->update([
                'paid_amount' => $paymentData['amount'] ?? $order->total_amount,
                'payment_method' => $paymentData['method'] ?? null,
                'paid_at' => now(),
            ]);

            return $this->loadOrderRelations($order);
        });
    }

    public function getStatistics(array $filters = []): array
    {
        $query = MoqOrder::query();

        $this->applyOrderFilters($query, $filters);

        $stats = $query
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(total_amount) as total_amount')
            ->selectRaw('SUM(total_quantity) as total_quantity')
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_count", [MoqOrder::STATUS_PENDING])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as confirmed_count", [MoqOrder::STATUS_CONFIRMED])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as processing_count", [MoqOrder::STATUS_PROCESSING])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as shipped_count", [MoqOrder::STATUS_SHIPPED])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_count", [MoqOrder::STATUS_COMPLETED])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled_count", [MoqOrder::STATUS_CANCELLED])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as refunded_count", [MoqOrder::STATUS_REFUNDED])
            ->selectRaw("SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as pending_amount", [MoqOrder::STATUS_PENDING])
            ->selectRaw("SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as completed_amount", [MoqOrder::STATUS_COMPLETED])
            ->first();

        return [
            'total_orders' => (int) $stats->total_count,
            'total_amount' => (float) $stats->total_amount,
            'total_quantity' => (int) $stats->total_quantity,
            'pending_count' => (int) $stats->pending_count,
            'confirmed_count' => (int) $stats->confirmed_count,
            'processing_count' => (int) $stats->processing_count,
            'shipped_count' => (int) $stats->shipped_count,
            'completed_count' => (int) $stats->completed_count,
            'cancelled_count' => (int) $stats->cancelled_count,
            'refunded_count' => (int) $stats->refunded_count,
            'pending_orders' => (int) $stats->pending_count,
            'processing_orders' => (int) $stats->processing_count,
            'shipped_orders' => (int) $stats->shipped_count,
            'completed_orders' => (int) $stats->completed_count,
            'pending_amount' => (float) $stats->pending_amount,
            'completed_amount' => (float) $stats->completed_amount,
        ];
    }

    protected function applyOrderFilters($query, array $filters): void
    {
        if (isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (!empty($filters['keyword'])) {
            $keyword = "%{$filters['keyword']}%";
            $query->where(function ($q) use ($keyword) {
                $q->where('order_no', 'like', $keyword)
                    ->orWhere('customer_name', 'like', $keyword)
                    ->orWhere('customer_phone', 'like', $keyword);
            });
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }
    }

    protected function loadOrderRelations(MoqOrder $order): MoqOrder
    {
        return $order->load('items', 'supplier', 'shipments');
    }

    protected function generateOrderNo(): string
    {
        return 'MOQ' . date('YmdHis') . Str::random(4);
    }

    protected function generateShipmentNo(): string
    {
        return 'SHP' . date('YmdHis') . Str::random(4);
    }
}
