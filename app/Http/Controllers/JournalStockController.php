<?php

namespace App\Http\Controllers;

use App\Models\JournalStock;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JournalStockController extends Controller
{
    /**
     * Display a listing of stock movements.
     */
    public function index(Request $request): JsonResponse
    {
        $query = JournalStock::with(['warehouse', 'product', 'documentHeader', 'createdBy']);

        // Filter by warehouse if provided
        if ($request->has('warehouse_id') && $request->warehouse_id !== '' && $request->warehouse_id !== null) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by product if provided
        if ($request->has('product_id') && $request->product_id !== '' && $request->product_id !== null) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by movement type if provided
        if ($request->has('movement_type') && $request->movement_type !== '' && $request->movement_type !== null) {
            $query->where('movement_type', $request->movement_type);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('movement_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('movement_date', '<=', $request->date_to);
        }

        // Filter by entry/exit
        if ($request->has('entry_type')) {
            if ($request->entry_type === 'entry') {
                $query->entries();
            } elseif ($request->entry_type === 'exit') {
                $query->exits();
            }
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('warehouse', function ($q) use ($search) {
                      $q->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // Pagination - order by movement_date desc, then id desc
        $perPage = $request->get('per_page', 15);
        $movements = $query->orderBy('movement_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $movements
        ]);
    }

    /**
     * Display the specified stock movement.
     */
    public function show(string $id): JsonResponse
    {
        $movement = JournalStock::with(['warehouse', 'product', 'documentHeader', 'documentLine', 'createdBy'])
            ->find($id);

        if (!$movement) {
            return response()->json([
                'success' => false,
                'message' => 'Stock movement not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $movement
        ]);
    }

    /**
     * Get stock history for a specific warehouse and product.
     */
    public function history(Request $request): JsonResponse
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $query = JournalStock::with(['documentHeader', 'createdBy'])
            ->where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id);

        if ($request->has('date_from')) {
            $query->where('movement_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('movement_date', '<=', $request->date_to);
        }

        $movements = $query->orderBy('movement_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $movements
        ]);
    }
}
