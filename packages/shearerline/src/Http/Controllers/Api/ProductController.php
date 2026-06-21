<?php

namespace Shearerline\Http\Controllers\Api;

use Illuminate\Http\Request;
use Shearerline\Http\Controllers\Controller as BaseController;
use Shearerline\Models\Product;
use Shearerline\Models\Supplier;

class ProductController extends BaseController
{
    public function index(Request $request)
    {
        $query = Product::with('supplier');

        if ($request->has('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->keyword}%")
                    ->orWhere('sku', 'like', "%{$request->keyword}%")
                    ->orWhere('description', 'like', "%{$request->keyword}%");
            });
        }

        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $perPage = $request->get('per_page', 15);
        $products = $query->latest()->paginate($perPage);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $products,
        ]);
    }

    public function bySupplier(Request $request, Supplier $supplier)
    {
        $query = $supplier->products();

        if ($request->has('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->keyword}%")
                    ->orWhere('sku', 'like', "%{$request->keyword}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $perPage = $request->get('per_page', 15);
        $products = $query->latest()->paginate($perPage);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:' . config('shearerline.tables.suppliers', 'shearerline_suppliers') . ',id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:' . config('shearerline.tables.products', 'shearerline_products') . ',sku',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'moq' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'image_url' => 'nullable|string|max:500',
            'specs' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $product = Product::create($validated);
        $product->load('supplier');

        return response()->json([
            'code' => 200,
            'message' => '产品创建成功',
            'data' => $product,
        ]);
    }

    public function show(Product $product)
    {
        $product->load('supplier', 'orderItems');

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $product,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'supplier_id' => 'sometimes|exists:' . config('shearerline.tables.suppliers', 'shearerline_suppliers') . ',id',
            'name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|max:100|unique:' . config('shearerline.tables.products', 'shearerline_products') . ',sku,' . $product->id,
            'description' => 'nullable|string|max:1000',
            'price' => 'sometimes|numeric|min:0',
            'moq' => 'sometimes|integer|min:1',
            'stock' => 'sometimes|integer|min:0',
            'unit' => 'sometimes|string|max:20',
            'image_url' => 'nullable|string|max:500',
            'specs' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);
        $product->load('supplier');

        return response()->json([
            'code' => 200,
            'message' => '产品更新成功',
            'data' => $product,
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'code' => 200,
            'message' => '产品删除成功',
            'data' => null,
        ]);
    }
}
