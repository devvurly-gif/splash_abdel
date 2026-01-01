<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Partner::query();

        // Filter by type if provided
        if ($request->has('type') && $request->type !== '' && $request->type !== null) {
            $query->byType($request->type);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '' && $request->status !== null) {
            $query->where('status', $request->boolean('status'));
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('legal_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('tax_id', 'like', "%{$search}%");
            });
        }

        // Pagination - order by id desc (newest first)
        $perPage = $request->get('per_page', 15);
        $partners = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $partners
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:customer,supplier,both',
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        // Code is always auto-generated (via model boot method)
        $partner = Partner::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Partner created successfully.',
            'data' => $partner
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $partner
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner not found.'
            ], 404);
        }

        $validated = $request->validate([
            'type' => 'sometimes|in:customer,supplier,both',
            'name' => 'sometimes|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        // Code cannot be updated - it's auto-generated
        unset($validated['code']);

        $partner->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Partner updated successfully.',
            'data' => $partner->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner not found.'
            ], 404);
        }

        // Check if partner has documents
        if ($partner->documents()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete partner with existing documents.'
            ], 422);
        }

        $partner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Partner deleted successfully.'
        ]);
    }
}
