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
use App\Http\Controllers\Api\EnumController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\Admin\Permission\PermissionController;
use App\Http\Controllers\Api\Admin\Role\RoleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Admin User API
Route::prefix('admin')->group(function () {
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

Route::prefix('admin')->group(function () {
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