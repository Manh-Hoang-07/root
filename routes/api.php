<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\Product\ProductController;
use App\Http\Controllers\Api\Admin\Warehouse\WarehouseController;
use App\Http\Controllers\Api\Admin\Order\OrderController;
use App\Http\Controllers\Api\Admin\User\UserController;
use App\Http\Controllers\Api\Admin\Category\CategoryController;
use App\Http\Controllers\Api\Admin\Brand\BrandController;
use App\Http\Controllers\Api\Admin\Attribute\AttributeController;
use App\Http\Controllers\Api\Admin\Attribute\AttributeValueController;
use App\Http\Controllers\Api\Core\EnumController;
use App\Http\Controllers\Api\Core\ImageController;
use App\Http\Controllers\Api\Admin\Permission\PermissionController;
use App\Http\Controllers\Api\Admin\Role\RoleController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\User\User\UserController as ApiUserController;
use App\Http\Controllers\Api\Admin\Inventory\InventoryController;

// Áp dụng CORS middleware cho tất cả routes
Route::middleware(['cors'])->group(function () {

// Public routes - không cần authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public API - thông tin công khai
Route::get('/products', [ProductController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Protected routes - cần authentication
Route::middleware(['auto.auth'])->group(function () {
    // User routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/change-password', [ApiUserController::class, 'changePassword']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);
    Route::post('/orders', [OrderController::class, 'store']);
});

// Admin User API - cần role admin
Route::middleware(['auto.auth', 'role:admin'])->prefix('admin')->group(function () { 
    Route::apiResource('users', UserController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('products', ProductController::class);
    Route::get('products/select/list', [ProductController::class, 'getForSelect']);
    Route::get('products/{product}/variants', [App\Http\Controllers\Api\Admin\Variant\VariantController::class, 'getProductVariants']);
    Route::get('products/{product}/images', [App\Http\Controllers\Api\Admin\Image\ImageController::class, 'getProductImages']);
    Route::apiResource('variants', App\Http\Controllers\Api\Admin\Variant\VariantController::class);
    Route::apiResource('images', App\Http\Controllers\Api\Admin\Image\ImageController::class);
    Route::apiResource('attributes', App\Http\Controllers\Api\Admin\Attribute\AttributeController::class);
    
    // Image upload
    Route::post('images/upload', [App\Http\Controllers\Api\Admin\Core\ImageController::class, 'upload']);
    // Search endpoints - phải đặt trước resource routes
    Route::get('brands/search', [BrandController::class, 'search']);
    Route::get('categories/search', [CategoryController::class, 'search']);
    
    Route::apiResource('warehouses', WarehouseController::class);
    
    // Inventory routes
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index']);
        
        // Filter options - phải đặt trước {inventory} route
        Route::get('/filter-options', [InventoryController::class, 'filterOptions']);
        
        // Special operations
        Route::post('/import', [InventoryController::class, 'import']);
        Route::post('/export', [InventoryController::class, 'export']);
        
        // Reports
        Route::get('/expiring-soon', [InventoryController::class, 'expiringSoon']);
        Route::get('/expired', [InventoryController::class, 'expired']);
        Route::get('/low-stock', [InventoryController::class, 'lowStock']);
        
        // CRUD operations - đặt sau các specific routes
        Route::get('/{inventory}', [InventoryController::class, 'show']);
        Route::post('/', [InventoryController::class, 'store']);
        Route::put('/{inventory}', [InventoryController::class, 'update']);
        Route::delete('/{inventory}', [InventoryController::class, 'destroy']);
    });
    
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('attributes', AttributeController::class);
    Route::apiResource('attribute-values', AttributeValueController::class);
    Route::patch('users/toggle-status/{id}', [UserController::class, 'toggleStatus']);
    Route::get('users/statuses', [UserController::class, 'statuses']);
    Route::post('users/{id}/change-password', [UserController::class, 'changePassword']);
    Route::get('users/{id}/with-roles', [UserController::class, 'showWithRoles']);
    Route::post('users/{id}/assign-roles', [UserController::class, 'assignRoles']);
});

Route::get('/enums/{type}', [EnumController::class, 'get']); 
Route::post('/upload-image', [ImageController::class, 'upload']);





// Admin routes for enum cache management
Route::middleware(['auto.auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::delete('/enums/{type}/cache', [EnumController::class, 'clearCache']);
    Route::delete('/enums/cache/all', [EnumController::class, 'clearAllCache']);
});

 

Route::middleware(['auto.auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::prefix('shipping')->group(function () {
        Route::apiResource('api', App\Http\Controllers\Api\Admin\Shipping\ShippingApiConfigController::class);
        Route::apiResource('services', App\Http\Controllers\Api\Admin\Shipping\ShippingServiceController::class);
        Route::apiResource('zones', App\Http\Controllers\Api\Admin\Shipping\ShippingZoneController::class);
        Route::apiResource('pricing', App\Http\Controllers\Api\Admin\Shipping\ShippingPricingRuleController::class);
        Route::apiResource('promotions', App\Http\Controllers\Api\Admin\Shipping\ShippingPromotionController::class);
        Route::apiResource('delivery', App\Http\Controllers\Api\Admin\Shipping\ShippingDeliverySettingController::class);
        Route::apiResource('advanced', App\Http\Controllers\Api\Admin\Shipping\ShippingAdvancedSettingController::class);
    });
}); 
});

// Đóng CORS middleware group