<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'brand', 'images']);

        // Filter by isactive if provided
        if ($request->has('isactive') && $request->isactive !== '' && $request->isactive !== null) {
            $query->where('isactive', $request->boolean('isactive'));
        }

        // Filter by category_id if provided
        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }

        // Filter by brand_id if provided
        if ($request->has('brand_id') && $request->brand_id !== '') {
            $query->where('brand_id', $request->brand_id);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('ean13', 'like', "%{$search}%");
            });
        }

        // Pagination - order by id desc (newest first)
        $perPage = $request->get('per_page', 10);
        $products = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ean13' => 'nullable|string|max:13',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'tax_id' => 'nullable|integer',
            'unit' => 'nullable|string|max:50',
            'isactive' => 'nullable|boolean',
            'onPromo' => 'nullable|boolean',
            'isFeatured' => 'nullable|boolean',
        ]);

        // Code is always auto-generated (via model boot method)
        $product = Product::create([
            'name' => $validated['name'],
            'ean13' => $validated['ean13'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'brand_id' => $validated['brand_id'] ?? null,
            'purchase_price' => $validated['purchase_price'] ?? 0,
            'sale_price' => $validated['sale_price'] ?? 0,
            'description' => $validated['description'] ?? null,
            'tax_id' => $validated['tax_id'] ?? null,
            'unit' => $validated['unit'] ?? null,
            'isactive' => $validated['isactive'] ?? true,
            'onPromo' => $validated['onPromo'] ?? false,
            'isFeatured' => $validated['isFeatured'] ?? false,
        ]);

        $product->load(['category', 'brand', 'images']);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with(['category', 'brand', 'images'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ean13' => 'nullable|string|max:13',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'tax_id' => 'nullable|integer',
            'unit' => 'nullable|string|max:50',
            'isactive' => 'nullable|boolean',
            'onPromo' => 'nullable|boolean',
            'isFeatured' => 'nullable|boolean',
        ]);

        $product->update([
            'name' => $validated['name'],
            'ean13' => $validated['ean13'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'brand_id' => $validated['brand_id'] ?? null,
            'purchase_price' => $validated['purchase_price'] ?? 0,
            'sale_price' => $validated['sale_price'] ?? 0,
            'description' => $validated['description'] ?? null,
            'tax_id' => $validated['tax_id'] ?? null,
            'unit' => $validated['unit'] ?? null,
            'isactive' => $validated['isactive'] ?? true,
            'onPromo' => $validated['onPromo'] ?? false,
            'isFeatured' => $validated['isFeatured'] ?? false,
        ]);

        $product->load(['category', 'brand', 'images']);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }
}
