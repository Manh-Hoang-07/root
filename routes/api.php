<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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
    Route::patch('users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus']);
});

// User (profile) API
Route::prefix('user')->group(function () {
    Route::get('profile', [UserProfileController::class, 'profile']);
    Route::put('profile', [UserProfileController::class, 'updateProfile']);
    Route::post('change-password', [UserProfileController::class, 'changePassword']);
}); 