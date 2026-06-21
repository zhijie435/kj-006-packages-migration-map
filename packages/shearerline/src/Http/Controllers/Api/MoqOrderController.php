<?php

namespace Shearerline\Http\Controllers\Api;

use Illuminate\Http\Request;
use Shearerline\Http\Controllers\Controller as BaseController;
use Shearerline\Models\MoqOrder;
use Shearerline\Services\MoqDirectShipService;

class MoqOrderController extends BaseController
{
    protected $moqService;

    public function __construct(MoqDirectShipService $moqService)
    {
        $this->moqService = $moqService;
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'status',
            'supplier_id',
            'keyword',
            'start_date',
            'end_date',
            'per_page',
        ]);

        $orders = $this->moqService->getOrderList($filters);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:' . config('shearerline.tables.suppliers', 'shearerline_suppliers') . ',id',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'customer_remark' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:' . config('shearerline.tables.products', 'shearerline_products') . ',id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.remark' => 'nullable|string|max:500',
        ]);

        try {
            $order = $this->moqService->createOrder($validated);

            return response()->json([
                'code' => 200,
                'message' => '订单创建成功',
                'data' => $order,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function show(MoqOrder $order)
    {
        $order = $this->moqService->getOrderDetail($order->id);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $order,
        ]);
    }

    public function confirm(MoqOrder $order)
    {
        try {
            $order = $this->moqService->confirmOrder($order->id);

            return response()->json([
                'code' => 200,
                'message' => '订单确认成功',
                'data' => $order,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function process(MoqOrder $order)
    {
        try {
            $order = $this->moqService->processOrder($order->id);

            return response()->json([
                'code' => 200,
                'message' => '订单开始处理',
                'data' => $order,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function ship(Request $request, MoqOrder $order)
    {
        $validated = $request->validate([
            'logistics_company' => 'required|string|max:100',
            'tracking_no' => 'required|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:' . config('shearerline.tables.moq_order_items', 'shearerline_moq_order_items') . ',id',
            'items.*.quantity' => 'required|integer|min:1',
            'weight' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'receiver_name' => 'nullable|string|max:100',
            'receiver_phone' => 'nullable|string|max:20',
            'receiver_address' => 'nullable|string|max:500',
            'remark' => 'nullable|string|max:500',
        ]);

        try {
            $shipment = $this->moqService->shipOrder($order->id, $validated);
            $order = $this->moqService->getOrderDetail($order->id);

            return response()->json([
                'code' => 200,
                'message' => '发货成功',
                'data' => [
                    'order' => $order,
                    'shipment' => $shipment,
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function complete(MoqOrder $order)
    {
        try {
            $order = $this->moqService->completeOrder($order->id);

            return response()->json([
                'code' => 200,
                'message' => '订单完成成功',
                'data' => $order,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function cancel(Request $request, MoqOrder $order)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $order = $this->moqService->cancelOrder($order->id, $validated['reason']);

            return response()->json([
                'code' => 200,
                'message' => '订单取消成功',
                'data' => $order,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function refund(Request $request, MoqOrder $order)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $order = $this->moqService->refundOrder($order->id, $validated['reason']);

            return response()->json([
                'code' => 200,
                'message' => '订单退款成功',
                'data' => $order,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function pay(Request $request, MoqOrder $order)
    {
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'method' => 'nullable|string|max:50',
        ]);

        try {
            $order = $this->moqService->payOrder($order->id, $validated);

            return response()->json([
                'code' => 200,
                'message' => '订单支付成功',
                'data' => $order,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function statistics(Request $request)
    {
        $filters = $request->only([
            'start_date',
            'end_date',
            'supplier_id',
        ]);

        $statistics = $this->moqService->getStatistics($filters);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $statistics,
        ]);
    }
}
