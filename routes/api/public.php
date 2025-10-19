<?php

use App\Http\Controllers\Api\Core\Enum\EnumController;
use App\Http\Controllers\Api\Core\File\FileController;
use App\Http\Controllers\Api\Core\Auth\AuthController;
use App\Http\Controllers\Api\Public\Menu\MenuController;
use App\Http\Controllers\Api\Public\Post\PostController;
use App\Http\Controllers\Api\Public\PostCategory\PostCategoryController;
use App\Http\Controllers\Api\Public\PostTag\PostTagController;
use App\Http\Controllers\Api\Public\Contact\ContactController;
use App\Http\Controllers\Api\Public\SystemConfig\SystemConfigController;
use App\Http\Controllers\Api\Public\Product\ProductController;
use App\Http\Controllers\Api\Public\Product\ProductCategoryController;
use App\Http\Controllers\Api\Public\Cart\CartController;
use App\Http\Controllers\Api\Public\Order\OrderController;
use Illuminate\Support\Facades\Route;

// Public API - Auth module
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public API - Enum
Route::prefix('enums')->group(function () {
    Route::get('/{type}', [EnumController::class, 'get']);
    Route::get('/types', [EnumController::class, 'getTypes']);
});

// Public API - File upload
Route::prefix('files')->group(function () {
    Route::post('/upload', [FileController::class, 'upload']);
    Route::post('/upload-multiple', [FileController::class, 'uploadMultiple']);
    Route::delete('/delete', [FileController::class, 'delete']);
});

// Public API - Post module
Route::apiResource('posts', PostController::class)->only(['index', 'show']);
Route::apiResource('menus', MenuController::class)->only(['index', 'show']);
Route::get('/posts/slug/{slug}', [PostController::class, 'showBySlug']);
Route::apiResource('post-categories', PostCategoryController::class)->only(['index', 'show']);
Route::get('/post-categories/slug/{slug}', [PostCategoryController::class, 'showBySlug']);
Route::apiResource('post-tags', PostTagController::class)->only(['index', 'show']);
Route::get('/post-tags/slug/{slug}', [PostTagController::class, 'showBySlug']);

// Public API - Contact module
Route::apiResource('contacts', ContactController::class)->only(['store']);

// Public API - System Config module (chỉ cho phép lấy configs theo group)
Route::prefix('system-configs')->group(function () {
    Route::get('/group/{group}', [SystemConfigController::class, 'getByGroup']);
});

// Public API - E-commerce module
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/by-category/{categoryId}', [ProductController::class, 'byCategory']);
Route::get('/products/{id}/variants', [ProductController::class, 'variants']);

Route::apiResource('product-categories', ProductCategoryController::class)->only(['index', 'show']);
Route::get('/product-categories/tree', [ProductCategoryController::class, 'tree']);
Route::get('/product-categories/{id}/products', [ProductCategoryController::class, 'products']);

// Cart API - Không cần đăng nhập
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/', [CartController::class, 'store']);
    Route::put('/{id}', [CartController::class, 'update']);
    Route::delete('/{id}', [CartController::class, 'destroy']);
    Route::delete('/', [CartController::class, 'clear']);
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon']);
    Route::delete('/remove-coupon', [CartController::class, 'removeCoupon']);
});

// Order API - Guest checkout
Route::apiResource('orders', OrderController::class)->only(['store', 'show']);
Route::prefix('orders')->group(function () {
    Route::post('/guest', [OrderController::class, 'guestCheckout']);
    Route::get('/guest/{orderNumber}/{email}', [OrderController::class, 'guestShow']);
    Route::post('/{id}/payment', [OrderController::class, 'processPayment']);
    Route::get('/status/{orderNumber}', [OrderController::class, 'getStatus']);
});
