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
        $this->checkPermission('viewAny', Shipment::class);

        $query = Shipment::with('order');

        if ($request->has('status')) {
            $query->whereStatus($request->status);
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

        $perPage = $request->get('per_page', config('shearerline.pagination.per_page', 15));
        $shipments = $query->latest()->paginate($perPage);

        return $this->paginated($shipments);
    }

    public function byOrder(Request $request, MoqOrder $order)
    {
        $this->checkPermission('view', $order);

        $shipments = $order->shipments()->latest()->get();

        return $this->success($shipments);
    }

    public function show(Shipment $shipment)
    {
        $this->checkPermission('view', $shipment);

        $shipment->load('order');

        return $this->success($shipment);
    }

    public function updateTracking(Request $request, Shipment $shipment)
    {
        $this->checkPermission('updateTracking', $shipment);

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

        $shipment = $this->moqService->updateTracking($shipment->id, $validated);

        return $this->success($shipment, '物流信息更新成功');
    }

    public function destroy(Shipment $shipment)
    {
        $this->checkPermission('delete', $shipment);

        if ($shipment->status !== Shipment::STATUS_PENDING) {
            return $this->error('只有待发货状态的物流记录可以删除', 400);
        }

        $shipment->delete();

        return $this->success(null, '物流记录删除成功');
    }
}
