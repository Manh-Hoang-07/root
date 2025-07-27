<?php

use Illuminate\Http\Request;
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
                    Route::get('/me', [ApiUserController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
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
    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('attributes', AttributeController::class);
    Route::apiResource('attribute-values', AttributeValueController::class);
    Route::patch('users/toggle-status/{id}', [UserController::class, 'toggleStatus']);
    Route::get('users/statuses', [UserController::class, 'statuses']);
    Route::post('users/{id}/change-password', [UserController::class, 'changePassword']);
});

Route::get('/enums/{type}', [EnumController::class, 'get']); 
Route::post('/upload-image', [ImageController::class, 'upload']);

// Test route để debug upload
Route::post('/test-upload', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Test upload endpoint working',
        'data' => [
            'files' => $request->allFiles(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]
    ]);
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