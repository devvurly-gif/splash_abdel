<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NumberingSystemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\JournalStockController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes (using Sanctum stateful middleware for session support)
Route::middleware(\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class)->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web');
    
    // Get authenticated user (using web guard for session-based auth)
    Route::middleware('auth:web')->get('/user', function (Request $request) {
        return $request->user();
    });
});

// Numbering System routes (protected routes)
Route::middleware([\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, 'auth:web'])->group(function () {
    Route::apiResource('numbering-systems', NumberingSystemController::class);
    Route::post('/numbering-systems/{id}/generate-next', [NumberingSystemController::class, 'generateNext']);
    Route::post('/numbering-systems/generate', [NumberingSystemController::class, 'generateByDomainAndType']);
    
    // Category routes
    Route::apiResource('categories', CategoryController::class);
    
    // Brand routes
    Route::apiResource('brands', BrandController::class);
    
    // Warehouse routes
    Route::apiResource('warehouses', WarehouseController::class);
    
    // Partner routes
    Route::apiResource('partners', PartnerController::class);
    
    // Product routes
    Route::apiResource('products', ProductController::class);
    
    // Product Image routes (nested under products)
    Route::get('/products/{productId}/images', [ProductImageController::class, 'index']);
    Route::post('/products/{productId}/images', [ProductImageController::class, 'store']);
    Route::post('/products/{productId}/images/upload-multiple', [ProductImageController::class, 'uploadMultiple']);
    Route::get('/products/{productId}/images/{id}', [ProductImageController::class, 'show']);
    Route::put('/products/{productId}/images/{id}', [ProductImageController::class, 'update']);
    Route::delete('/products/{productId}/images/{id}', [ProductImageController::class, 'destroy']);
    
    // Document routes
    Route::apiResource('documents', DocumentController::class);
    Route::post('/documents/{id}/validate', [DocumentController::class, 'validateDocument']);
    Route::post('/documents/{id}/cancel', [DocumentController::class, 'cancel']);
    Route::get('/documents/preview-code', [DocumentController::class, 'previewCode']);
    
    // Journal Stock routes
    Route::get('/journal-stock', [JournalStockController::class, 'index']);
    Route::get('/journal-stock/{id}', [JournalStockController::class, 'show']);
    Route::get('/journal-stock/history', [JournalStockController::class, 'history']);
});
