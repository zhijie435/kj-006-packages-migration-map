<?php

namespace Shearerline\Http\Controllers\Api;

use Illuminate\Http\Request;
use Shearerline\Http\Controllers\Controller as BaseController;
use Shearerline\Models\Supplier;

class SupplierController extends BaseController
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->keyword}%")
                    ->orWhere('code', 'like', "%{$request->keyword}%")
                    ->orWhere('contact_name', 'like', "%{$request->keyword}%")
                    ->orWhere('contact_phone', 'like', "%{$request->keyword}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $perPage = $request->get('per_page', 15);
        $suppliers = $query->latest()->paginate($perPage);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $suppliers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:' . config('shearerline.tables.suppliers', 'shearerline_suppliers') . ',code',
            'contact_name' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'remark' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json([
            'code' => 200,
            'message' => '供应商创建成功',
            'data' => $supplier,
        ]);
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['products', 'orders']);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:' . config('shearerline.tables.suppliers', 'shearerline_suppliers') . ',code,' . $supplier->id,
            'contact_name' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'remark' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $supplier->update($validated);

        return response()->json([
            'code' => 200,
            'message' => '供应商更新成功',
            'data' => $supplier,
        ]);
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->json([
            'code' => 200,
            'message' => '供应商删除成功',
            'data' => null,
        ]);
    }
}
