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
        $query = Warehouse::query();

        // Filter by status if provided (and not empty string)
        if ($request->has('status') && $request->status !== '' && $request->status !== null) {
            $query->where('status', $request->boolean('status'));
        }
        // Show all warehouses by default (no status filter)

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
            'status' => 'nullable|boolean',
        ]);

        // Code is always auto-generated (via model boot method)
        $warehouse = Warehouse::create([
            'title' => $validated['title'],
            'status' => $validated['status'] ?? true,
        ]);

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
        $warehouse = Warehouse::find($id);

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
            'status' => 'nullable|boolean',
        ]);

        // Code cannot be updated - it's auto-generated
        unset($validated['code']);

        $warehouse->update($validated);

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
}

