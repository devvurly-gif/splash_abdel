<?php

namespace App\Http\Controllers;

use App\Models\DocumentHeader;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = DocumentHeader::with(['warehouse', 'partner', 'lines.product']);

        // Filter by domain if provided
        if ($request->has('domain') && $request->domain !== '' && $request->domain !== null) {
            $query->where('domain', $request->domain);
        }

        // Filter by type if provided
        if ($request->has('type') && $request->type !== '' && $request->type !== null) {
            $query->where('type', $request->type);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '' && $request->status !== null) {
            $query->where('status', $request->status);
        }

        // Filter by warehouse if provided
        if ($request->has('warehouse_id') && $request->warehouse_id !== '' && $request->warehouse_id !== null) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by partner if provided
        if ($request->has('partner_id') && $request->partner_id !== '' && $request->partner_id !== null) {
            $query->where('partner_id', $request->partner_id);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('document_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('document_date', '<=', $request->date_to);
        }

        // Pagination - order by id desc (newest first)
        $perPage = $request->get('per_page', 15);
        $documents = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'domain' => 'required|in:sale,purchase,stock',
            'type' => 'required|string',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'partner_id' => 'nullable|exists:partners,id',
            'document_date' => 'required|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.quantity' => 'required|numeric|min:0.001',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.unit_cost' => 'nullable|numeric|min:0',
            'lines.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.description' => 'nullable|string',
            'lines.*.notes' => 'nullable|string',
        ]);

        try {
            $document = $this->documentService->createDocument($validated);

            return response()->json([
                'success' => true,
                'message' => 'Document created successfully.',
                'data' => $document->load(['warehouse', 'partner', 'lines.product'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $document = DocumentHeader::with(['warehouse', 'partner', 'lines.product', 'createdBy', 'validatedBy'])
            ->find($id);

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $document
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'partner_id' => 'nullable|exists:partners,id',
            'document_date' => 'sometimes|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
            'lines' => 'sometimes|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.quantity' => 'required|numeric|min:0.001',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.unit_cost' => 'nullable|numeric|min:0',
            'lines.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.description' => 'nullable|string',
            'lines.*.notes' => 'nullable|string',
        ]);

        try {
            $document = $this->documentService->updateDocument($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Document updated successfully.',
                'data' => $document->load(['warehouse', 'partner', 'lines.product'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $document = DocumentHeader::find($id);

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found.'
            ], 404);
        }

        // Only allow deletion of draft documents
        if ($document->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft documents can be deleted.'
            ], 422);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.'
        ]);
    }

    /**
     * Validate a document (create stock movements)
     */
    public function validateDocument(string $id): JsonResponse
    {
        try {
            $document = $this->documentService->validateDocument($id);

            return response()->json([
                'success' => true,
                'message' => 'Document validated successfully.',
                'data' => $document->load(['warehouse', 'partner', 'lines.product'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Cancel a document
     */
    public function cancel(string $id): JsonResponse
    {
        try {
            $document = $this->documentService->cancelDocument($id);

            return response()->json([
                'success' => true,
                'message' => 'Document cancelled successfully.',
                'data' => $document->load(['warehouse', 'partner', 'lines.product'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
