<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\EnumController;
use App\Http\Controllers\Api\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\User\UserController as UserProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Product API Routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/featured', [ProductController::class, 'featured']);
    Route::get('/{product}', [ProductController::class, 'show']);
    Route::get('/{product}/inventory', [ProductController::class, 'inventory']);
});

// Warehouse API Routes
Route::prefix('warehouses')->group(function () {
    Route::get('/', [WarehouseController::class, 'index']);
    Route::get('/{warehouse}', [WarehouseController::class, 'show']);
    Route::get('/{warehouse}/products', [WarehouseController::class, 'products']);
    Route::get('/{warehouse}/stats', [WarehouseController::class, 'stats']);
});

// Order API Routes
Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/stats', [OrderController::class, 'stats']);
    Route::get('/{order}', [OrderController::class, 'show']);
    Route::get('/{order}/status-history', [OrderController::class, 'statusHistory']);
});

// User API Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{user}', [UserController::class, 'show']);
    Route::get('/{user}/orders', [UserController::class, 'orders']);
    Route::get('/{user}/stats', [UserController::class, 'stats']);
});

// Shipping API Routes
Route::prefix('shipping')->group(function () {
    Route::get('/zones', [ShippingController::class, 'index']);
    Route::get('/zones/{shippingZone}', [ShippingController::class, 'show']);
    Route::post('/calculate', [ShippingController::class, 'calculateShipping']);
    Route::get('/available-zones', [ShippingController::class, 'availableZones']);
});

// Legacy routes (for backward compatibility)
Route::get('/users', function () {
    return User::all();
});

Route::post('/users', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:8',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
    ]);

    return response()->json($user, 201);
});

Route::put('/users/{id}', function (Request $request, $id) {
    $user = User::findOrFail($id);
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
    ]);

    $user->update($validated);
    return response()->json($user);
});

Route::delete('/users/{id}', function ($id) {
    $user = User::findOrFail($id);
    $user->delete();
    return response()->json(['message' => 'User deleted successfully']);
});

// Admin User API
Route::prefix('admin')->group(function () {
    Route::apiResource('users', AdminUserController::class);
    Route::apiResource('permissions', AdminPermissionController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('products', \App\Http\Controllers\Api\Admin\ProductController::class);
    Route::apiResource('warehouses', \App\Http\Controllers\Api\Admin\WarehouseController::class);
    Route::apiResource('orders', \App\Http\Controllers\Api\Admin\OrderController::class);
    Route::apiResource('promotions', \App\Http\Controllers\Api\Admin\PromotionController::class);
    Route::apiResource('shipping-promotions', \App\Http\Controllers\Api\Admin\ShippingPromotionController::class);
    Route::apiResource('shipping-infos', \App\Http\Controllers\Api\Admin\ShippingInfoController::class);
    Route::apiResource('categories', \App\Http\Controllers\Api\Admin\CategoryController::class); // Thêm dòng này
    Route::apiResource('brands', \App\Http\Controllers\Api\Admin\BrandController::class); // Thêm dòng này
    Route::apiResource('attributes', \App\Http\Controllers\Api\Admin\AttributeController::class); // Thêm dòng này
    Route::apiResource('attribute-values', \App\Http\Controllers\Api\Admin\AttributeValueController::class); // Thêm dòng này
    Route::patch('users/toggle-status/{id}', [AdminUserController::class, 'toggleStatus']);
    Route::get('users/statuses', [AdminUserController::class, 'statuses']);
    Route::post('users/{id}/change-password', [AdminUserController::class, 'changePassword']);
    Route::get('products/{product}/variants', [\App\Http\Controllers\Api\Admin\ProductController::class, 'variants']);
    Route::post('products/{product}/variants', [\App\Http\Controllers\Api\Admin\ProductController::class, 'storeVariant']);
    Route::put('products/{product}/variants/{variant}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'updateVariant']);
    Route::delete('products/{product}/variants/{variant}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'destroyVariant']);
    Route::get('products/{product}/gallery', [\App\Http\Controllers\Api\Admin\ProductController::class, 'gallery']);
    Route::post('products/{product}/gallery', [\App\Http\Controllers\Api\Admin\ProductController::class, 'addGalleryImage']);
    Route::delete('products/{product}/gallery/{image}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'deleteGalleryImage']);
    Route::get('inventory/product/{product}', [\App\Http\Controllers\Api\Admin\InventoryController::class, 'productInventory']);
    Route::get('inventory/warehouse/{warehouse}', [\App\Http\Controllers\Api\Admin\InventoryController::class, 'warehouseInventory']);
    Route::post('inventory/adjust', [\App\Http\Controllers\Api\Admin\InventoryController::class, 'adjust']);
    Route::post('orders/{order}/update-status', [\App\Http\Controllers\Api\Admin\OrderController::class, 'updateStatus']);
    Route::get('orders/{order}/items', [\App\Http\Controllers\Api\Admin\OrderController::class, 'orderItems']);
    Route::post('promotions/{promotion}/assign-products', [\App\Http\Controllers\Api\Admin\PromotionController::class, 'assignProducts']);
    Route::post('shipping-infos/{shippingInfo}/update-status', [\App\Http\Controllers\Api\Admin\ShippingInfoController::class, 'updateStatus']);
    Route::get('shipping-infos/{shippingInfo}/history', [\App\Http\Controllers\Api\Admin\ShippingInfoController::class, 'history']);
});

// User (profile) API
Route::prefix('user')->group(function () {
    Route::get('profile', [UserProfileController::class, 'profile']);
    Route::put('profile', [UserProfileController::class, 'updateProfile']);
    Route::post('change-password', [UserProfileController::class, 'changePassword']);
}); 

Route::get('/enums/{type}', [EnumController::class, 'get']); 
Route::post('/upload-image', [\App\Http\Controllers\Api\ImageController::class, 'upload']); 

Route::prefix('admin')->group(function () {
    Route::prefix('shipping')->group(function () {
        Route::apiResource('api', App\Http\Controllers\Api\Admin\Shipping\ApiController::class);
        Route::apiResource('services', App\Http\Controllers\Api\Admin\Shipping\ServiceController::class);
        Route::apiResource('zones', App\Http\Controllers\Api\Admin\Shipping\ZoneController::class);
        Route::apiResource('pricing', App\Http\Controllers\Api\Admin\Shipping\PricingController::class);
        Route::apiResource('promotions', App\Http\Controllers\Api\Admin\Shipping\PromotionController::class);
        Route::apiResource('delivery', App\Http\Controllers\Api\Admin\Shipping\DeliveryController::class);
        Route::apiResource('advanced', App\Http\Controllers\Api\Admin\Shipping\AdvancedController::class);
    });
}); 