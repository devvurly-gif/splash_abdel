<?php

namespace App\Http\Controllers;

use App\Models\NumberingSystem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class NumberingSystemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = NumberingSystem::query();

        // Filter by domain if provided
        if ($request->has('domain')) {
            $query->where('domain', $request->domain);
        }

        // Filter by type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by active status if provided
        if ($request->has('isActive')) {
            $query->where('isActive', $request->boolean('isActive'));
        } else {
            // Default to active only
            $query->where('isActive', true);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('domain', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $numberingSystems = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $numberingSystems
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'domain' => ['required', 'string', 'in:structure,sale,purchase,stock'],
            'type' => 'required|string|max:255',
            'template' => 'required|string|max:255',
            'next_trick' => 'nullable|integer|min:1',
            'isActive' => 'nullable|boolean',
        ]);

        // Check for duplicate domain and type combination
        $exists = NumberingSystem::where('domain', $validated['domain'])
            ->where('type', $validated['type'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A numbering system with this domain and type already exists.'
            ], 422);
        }

        $numberingSystem = NumberingSystem::create([
            'title' => $validated['title'],
            'domain' => $validated['domain'],
            'type' => $validated['type'],
            'template' => $validated['template'],
            'next_trick' => $validated['next_trick'] ?? 1,
            'isActive' => $validated['isActive'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Numbering system created successfully.',
            'data' => $numberingSystem
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $numberingSystem = NumberingSystem::find($id);

        if (!$numberingSystem) {
            return response()->json([
                'success' => false,
                'message' => 'Numbering system not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $numberingSystem
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $numberingSystem = NumberingSystem::find($id);

        if (!$numberingSystem) {
            return response()->json([
                'success' => false,
                'message' => 'Numbering system not found.'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'domain' => ['sometimes', 'string', 'in:structure,sales,purchase,stock'],
            'type' => 'sometimes|string|max:255',
            'template' => 'sometimes|string|max:255',
            'next_trick' => 'nullable|integer|min:1',
            'isActive' => 'nullable|boolean',
        ]);

        // Check for duplicate domain and type combination (excluding current record)
        if (isset($validated['domain']) || isset($validated['type'])) {
            $domain = $validated['domain'] ?? $numberingSystem->domain;
            $type = $validated['type'] ?? $numberingSystem->type;

            $exists = NumberingSystem::where('domain', $domain)
                ->where('type', $type)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'A numbering system with this domain and type already exists.'
                ], 422);
            }
        }

        $numberingSystem->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Numbering system updated successfully.',
            'data' => $numberingSystem->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $numberingSystem = NumberingSystem::find($id);

        if (!$numberingSystem) {
            return response()->json([
                'success' => false,
                'message' => 'Numbering system not found.'
            ], 404);
        }

        $numberingSystem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Numbering system deleted successfully.'
        ]);
    }

    /**
     * Generate the next number for a specific numbering system.
     */
    public function generateNext(string $id): JsonResponse
    {
        $numberingSystem = NumberingSystem::find($id);

        if (!$numberingSystem) {
            return response()->json([
                'success' => false,
                'message' => 'Numbering system not found.'
            ], 404);
        }

        if (!$numberingSystem->isActive) {
            return response()->json([
                'success' => false,
                'message' => 'Numbering system is not active.'
            ], 422);
        }

        $nextNumber = $numberingSystem->getNextNumber();

        return response()->json([
            'success' => true,
            'data' => [
                'number' => $nextNumber,
                'next_trick' => $numberingSystem->next_trick
            ]
        ]);
    }

    /**
     * Generate next number by domain and type.
     */
    public function generateByDomainAndType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'domain' => 'required|string',
            'type' => 'required|string',
        ]);

        $numberingSystem = NumberingSystem::byDomainAndType(
            $validated['domain'],
            $validated['type']
        )->active()->first();

        if (!$numberingSystem) {
            return response()->json([
                'success' => false,
                'message' => 'Numbering system not found for the specified domain and type.'
            ], 404);
        }

        $nextNumber = $numberingSystem->getNextNumber();

        return response()->json([
            'success' => true,
            'data' => [
                'number' => $nextNumber,
                'next_trick' => $numberingSystem->next_trick,
                'numbering_system' => $numberingSystem
            ]
        ]);
    }
}
