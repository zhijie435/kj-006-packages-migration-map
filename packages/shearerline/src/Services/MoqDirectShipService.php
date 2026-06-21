<?php

namespace Shearerline\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
                    throw new \InvalidArgumentException("产品 {$product->name} 未达到最小起订量 {$product->moq}");
                }

                if (!$product->hasEnoughStock($item['quantity'])) {
                    throw new \InvalidArgumentException("产品 {$product->name} 库存不足");
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
                'status' => MoqOrder::STATUS_PENDING,
                'created_by' => auth()->id() ?? null,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            return $order->load('items', 'supplier', 'shipments');
        });
    }

    public function getOrderList(array $filters = [])
    {
        $query = MoqOrder::with('items', 'supplier', 'shipments');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (isset($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('order_no', 'like', "%{$filters['keyword']}%")
                    ->orWhere('customer_name', 'like', "%{$filters['keyword']}%")
                    ->orWhere('customer_phone', 'like', "%{$filters['keyword']}%");
            });
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $perPage = $filters['per_page'] ?? 15;

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
                throw new \InvalidArgumentException('订单状态不允许确认');
            }

            $order->update([
                'status' => MoqOrder::STATUS_CONFIRMED,
                'confirmed_at' => now(),
            ]);

            return $order->load('items', 'supplier', 'shipments');
        });
    }

    public function processOrder(int $orderId): MoqOrder
    {
        return DB::transaction(function () use ($orderId) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canProcess()) {
                throw new \InvalidArgumentException('订单状态不允许开始处理');
            }

            $order->update([
                'status' => MoqOrder::STATUS_PROCESSING,
                'processed_at' => now(),
            ]);

            return $order->load('items', 'supplier', 'shipments');
        });
    }

    public function shipOrder(int $orderId, array $shipmentData): Shipment
    {
        return DB::transaction(function () use ($orderId, $shipmentData) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canShip()) {
                throw new \InvalidArgumentException('订单状态不允许发货');
            }

            $shipQuantity = 0;
            $shipItems = [];

            foreach ($shipmentData['items'] as $item) {
                $orderItem = MoqOrderItem::findOrFail($item['order_item_id']);
                $remainingQuantity = $orderItem->quantity - $orderItem->shipped_quantity;

                if ($item['quantity'] > $remainingQuantity) {
                    throw new \InvalidArgumentException("发货数量超过未发货数量");
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

            $orderStatus = $isFullyShipped ? MoqOrder::STATUS_SHIPPED : MoqOrder::STATUS_PROCESSING;

            $order->update([
                'shipped_quantity' => $newShippedQuantity,
                'status' => $orderStatus,
                'shipped_at' => $isFullyShipped ? now() : $order->shipped_at,
            ]);

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
                'shipped_at' => now(),
            ]);

            return $shipment;
        });
    }

    public function updateTracking(int $shipmentId, array $trackingData): Shipment
    {
        return DB::transaction(function () use ($shipmentId, $trackingData) {
            $shipment = Shipment::findOrFail($shipmentId);

            if (!$shipment->canUpdateTracking()) {
                throw new \InvalidArgumentException('物流状态不允许更新');
            }

            $updateData = [];

            if (isset($trackingData['logistics_company'])) {
                $updateData['logistics_company'] = $trackingData['logistics_company'];
            }

            if (isset($trackingData['tracking_no'])) {
                $updateData['tracking_no'] = $trackingData['tracking_no'];
            }

            if (isset($trackingData['status'])) {
                $updateData['status'] = $trackingData['status'];

                if ($trackingData['status'] === Shipment::STATUS_DELIVERED) {
                    $updateData['delivered_at'] = now();
                }
            }

            if (isset($trackingData['remark'])) {
                $updateData['remark'] = $trackingData['remark'];
            }

            $shipment->update($updateData);

            $shipment->load('order');
            $order = $shipment->order;

            if ($order) {
                $order->refresh();
                $order->load('shipments');

                $allDelivered = $order->shipments->every(function ($s) {
                    return $s->status === Shipment::STATUS_DELIVERED;
                });

                if ($allDelivered && $order->canComplete()) {
                    $order->update([
                        'status' => MoqOrder::STATUS_COMPLETED,
                        'completed_at' => now(),
                    ]);
                }
            }

            return $shipment->load('order.items', 'order.supplier', 'order.shipments');
        });
    }

    public function completeOrder(int $orderId): MoqOrder
    {
        return DB::transaction(function () use ($orderId) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canComplete()) {
                throw new \InvalidArgumentException('订单状态不允许完成');
            }

            $order->update([
                'status' => MoqOrder::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            return $order->load('items', 'supplier', 'shipments');
        });
    }

    public function cancelOrder(int $orderId, string $reason): MoqOrder
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canCancel()) {
                throw new \InvalidArgumentException('订单状态不允许取消');
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

            $order->update([
                'status' => MoqOrder::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancelled_reason' => $reason,
            ]);

            return $order->load('items', 'supplier', 'shipments');
        });
    }

    public function refundOrder(int $orderId, string $reason): MoqOrder
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canRefund()) {
                throw new \InvalidArgumentException('订单状态不允许退款');
            }

            $order->update([
                'status' => MoqOrder::STATUS_REFUNDED,
                'refunded_at' => now(),
                'refunded_reason' => $reason,
            ]);

            return $order->load('items', 'supplier', 'shipments');
        });
    }

    public function payOrder(int $orderId, array $paymentData): MoqOrder
    {
        return DB::transaction(function () use ($orderId, $paymentData) {
            $order = MoqOrder::findOrFail($orderId);

            if (!$order->canPay()) {
                throw new \InvalidArgumentException('订单状态不允许支付或已支付');
            }

            $order->update([
                'paid_amount' => $paymentData['amount'] ?? $order->total_amount,
                'payment_method' => $paymentData['method'] ?? null,
                'paid_at' => now(),
            ]);

            return $order->load('items', 'supplier', 'shipments');
        });
    }

    public function getStatistics(array $filters = []): array
    {
        $query = MoqOrder::query();

        if (isset($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        $cloneQuery = clone $query;

        return [
            'total_orders' => $cloneQuery->count(),
            'total_amount' => $cloneQuery->sum('total_amount'),
            'total_quantity' => $cloneQuery->sum('total_quantity'),
            'pending_count' => (clone $query)->pending()->count(),
            'confirmed_count' => (clone $query)->confirmed()->count(),
            'processing_count' => (clone $query)->processing()->count(),
            'shipped_count' => (clone $query)->shipped()->count(),
            'completed_count' => (clone $query)->completed()->count(),
            'cancelled_count' => (clone $query)->cancelled()->count(),
            'refunded_count' => (clone $query)->refunded()->count(),
            'pending_orders' => (clone $query)->pending()->count(),
            'processing_orders' => (clone $query)->processing()->count(),
            'shipped_orders' => (clone $query)->shipped()->count(),
            'completed_orders' => (clone $query)->completed()->count(),
            'pending_amount' => (clone $query)->pending()->sum('total_amount'),
            'completed_amount' => (clone $query)->completed()->sum('total_amount'),
        ];
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
