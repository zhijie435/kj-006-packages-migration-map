<?php

namespace Shearerline\Http\Controllers\Api;

use Illuminate\Http\Request;
use Shearerline\Http\Controllers\Controller as BaseController;
use Shearerline\Models\MoqOrder;
use Shearerline\Models\Shipment;
use Shearerline\Services\MoqDirectShipService;

class ShipmentController extends BaseController
{
    protected $moqService;

    public function __construct(MoqDirectShipService $moqService)
    {
        $this->moqService = $moqService;
    }

    public function index(Request $request)
    {
        $query = Shipment::with('order');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('logistics_company')) {
            $query->where('logistics_company', 'like', "%{$request->logistics_company}%");
        }

        if ($request->has('tracking_no')) {
            $query->where('tracking_no', 'like', "%{$request->tracking_no}%");
        }

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $perPage = $request->get('per_page', 15);
        $shipments = $query->latest()->paginate($perPage);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $shipments,
        ]);
    }

    public function byOrder(Request $request, MoqOrder $order)
    {
        $shipments = $order->shipments()->latest()->get();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $shipments,
        ]);
    }

    public function show(Shipment $shipment)
    {
        $shipment->load('order');

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $shipment,
        ]);
    }

    public function updateTracking(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'logistics_company' => 'sometimes|string|max:100',
            'tracking_no' => 'sometimes|string|max:100',
            'status' => 'sometimes|in:' . implode(',', [
                Shipment::STATUS_PENDING,
                Shipment::STATUS_SHIPPED,
                Shipment::STATUS_IN_TRANSIT,
                Shipment::STATUS_DELIVERED,
                Shipment::STATUS_RETURNED,
                Shipment::STATUS_FAILED,
            ]),
            'remark' => 'nullable|string|max:500',
        ]);

        try {
            $shipment = $this->moqService->updateTracking($shipment->id, $validated);

            return response()->json([
                'code' => 200,
                'message' => '物流信息更新成功',
                'data' => $shipment,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    public function destroy(Shipment $shipment)
    {
        if ($shipment->status !== Shipment::STATUS_PENDING) {
            return response()->json([
                'code' => 400,
                'message' => '只有待发货状态的物流记录可以删除',
                'data' => null,
            ], 400);
        }

        $shipment->delete();

        return response()->json([
            'code' => 200,
            'message' => '物流记录删除成功',
            'data' => null,
        ]);
    }
}
