<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Warehouse::with('incharge');

        // Filter by type if provided
        if ($request->has('type') && $request->type !== '' && $request->type !== null) {
            $query->where('type', $request->type);
        }

        // Filter by isprincipal if provided
        if ($request->has('isprincipal') && $request->isprincipal !== '' && $request->isprincipal !== null) {
            $query->where('isprincipal', $request->boolean('isprincipal'));
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Pagination - order by id desc (newest first)
        $perPage = $request->get('per_page', 15);
        $warehouses = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $warehouses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isprincipal' => 'nullable|boolean',
            'inchargeOf' => 'nullable|exists:users,id',
            'type' => 'required|in:warehouse,store,tank',
        ]);

        // Code is always auto-generated (via model boot method)
        $warehouse = Warehouse::create([
            'title' => $validated['title'],
            'isprincipal' => $validated['isprincipal'] ?? false,
            'inchargeOf' => $validated['inchargeOf'] ?? null,
            'type' => $validated['type'],
        ]);

        // Load the relationship
        $warehouse->load('incharge');

        return response()->json([
            'success' => true,
            'message' => 'Warehouse created successfully.',
            'data' => $warehouse
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $warehouse = Warehouse::with('incharge')->find($id);

        if (!$warehouse) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $warehouse
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found.'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'isprincipal' => 'nullable|boolean',
            'inchargeOf' => 'nullable|exists:users,id',
            'type' => 'sometimes|in:warehouse,store,tank',
        ]);

        // Code cannot be updated - it's auto-generated
        unset($validated['code']);

        $warehouse->update($validated);

        // Load the relationship
        $warehouse->load('incharge');

        return response()->json([
            'success' => true,
            'message' => 'Warehouse updated successfully.',
            'data' => $warehouse->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found.'
            ], 404);
        }

        $warehouse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Warehouse deleted successfully.'
        ]);
    }

    /**
     * Get products in a warehouse.
     */
    public function products(string $id, Request $request): JsonResponse
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found.'
            ], 404);
        }

        $query = $warehouse->products()->with(['category', 'brand', 'images']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('products.code', 'like', "%{$search}%")
                  ->orWhere('products.name', 'like', "%{$search}%")
                  ->orWhere('products.ean13', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->orderBy('products.id', 'desc')->paginate($perPage);
        
        // Add pivot data to each product
        $products->getCollection()->transform(function ($product) {
            $product->quantity = $product->pivot->quantity ?? 0;
            $product->cmup = $product->pivot->cmup ?? 0;
            $product->total = ($product->quantity * $product->cmup);
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'warehouse' => $warehouse,
                'products' => $products
            ]
        ]);
    }
}

