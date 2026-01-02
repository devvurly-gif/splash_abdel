<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Display a listing of images for a product.
     */
    public function index(string $productId): JsonResponse
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $images = $product->images()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $images
        ]);
    }

    /**
     * Store a newly created image.
     */
    public function store(Request $request, string $productId): JsonResponse
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'alt' => 'nullable|string|max:255',
            'isprimary' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $imageUrl = null;
        $title = null;
        $alt = null;

        // Handle file upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('products/images', $filename, 'public');
            $imageUrl = asset('storage/' . $path);
            
            // Auto-generate title from filename
            $title = $originalName;
            
            // Auto-generate alt from product name
            $product = Product::find($productId);
            $alt = $product ? $product->name : $originalName;
        } elseif ($request->has('url')) {
            $imageUrl = $validated['url'];
            $title = $validated['title'] ?? null;
            $alt = $validated['alt'] ?? null;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Either image file or URL is required.'
            ], 422);
        }

        // If this is set as primary, unset other primary images
        if ($validated['isprimary'] ?? false) {
            ProductImage::where('product_id', $productId)
                ->where('isprimary', true)
                ->update(['isprimary' => false]);
        }

        $image = ProductImage::create([
            'product_id' => $productId,
            'title' => $title,
            'url' => $imageUrl,
            'alt' => $alt,
            'isprimary' => $validated['isprimary'] ?? false,
            'order' => $validated['order'] ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product image created successfully.',
            'data' => $image
        ], 201);
    }

    /**
     * Display the specified image.
     */
    public function show(string $productId, string $id): JsonResponse
    {
        $image = ProductImage::where('product_id', $productId)->find($id);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Product image not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $image
        ]);
    }

    /**
     * Update the specified image.
     */
    public function update(Request $request, string $productId, string $id): JsonResponse
    {
        $image = ProductImage::where('product_id', $productId)->find($id);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Product image not found.'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'alt' => 'nullable|string|max:255',
            'isprimary' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            // Delete old image file if exists
            if ($image->url) {
                $oldPath = str_replace(asset('storage/'), '', $image->url);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('products/images', $filename, 'public');
            $validated['url'] = asset('storage/' . $path);
        }

        // If this is set as primary, unset other primary images
        if (isset($validated['isprimary']) && $validated['isprimary']) {
            ProductImage::where('product_id', $productId)
                ->where('id', '!=', $id)
                ->where('isprimary', true)
                ->update(['isprimary' => false]);
        }

        // Remove image from validated if it was a file upload (already converted to url)
        unset($validated['image']);
        $image->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product image updated successfully.',
            'data' => $image->fresh()
        ]);
    }

    /**
     * Remove the specified image.
     */
    public function destroy(string $productId, string $id): JsonResponse
    {
        $image = ProductImage::where('product_id', $productId)->find($id);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Product image not found.'
            ], 404);
        }

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product image deleted successfully.'
        ]);
    }

    /**
     * Upload multiple images at once.
     */
    public function uploadMultiple(Request $request, string $productId): JsonResponse
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'primary_index' => 'nullable|integer|min:0',
        ]);

        $uploadedImages = [];
        $primaryIndex = $request->input('primary_index');

        // If primary index is set, unset other primary images
        if ($primaryIndex !== null) {
            ProductImage::where('product_id', $productId)
                ->where('isprimary', true)
                ->update(['isprimary' => false]);
        }

        foreach ($request->file('images') as $index => $file) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = time() . '_' . uniqid() . '_' . $index . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('products/images', $filename, 'public');
            $imageUrl = asset('storage/' . $path);

            $image = ProductImage::create([
                'product_id' => $productId,
                'title' => $originalName, // Nom du fichier comme title
                'url' => $imageUrl, // Lien storage
                'alt' => $product->name, // Nom du produit comme alt
                'isprimary' => ($primaryIndex !== null && $index == $primaryIndex),
                'order' => $index,
            ]);

            $uploadedImages[] = $image;
        }

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully.',
            'data' => $uploadedImages
        ], 201);
    }
}
