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
use App\Http\Controllers\Api\Core\Enum\EnumController;
use App\Http\Controllers\Api\Core\File\FileController;
use App\Http\Controllers\Api\Admin\Permission\PermissionController;
use App\Http\Controllers\Api\Admin\Role\RoleController;
use App\Http\Controllers\Api\Core\Auth\AuthController;
use App\Http\Controllers\Api\User\User\UserController as ApiUserController;
use App\Http\Controllers\Api\Admin\Inventory\InventoryController;
use App\Http\Controllers\Api\Public\Contact\ContactController as PublicContactController;
use App\Http\Controllers\Api\Admin\Contact\ContactController;

// CORS preflight route
Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization, X-XSRF-TOKEN')
        ->header('Access-Control-Allow-Credentials', 'true');
})->where('any', '.*');

// Public routes - không cần authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public API - posts
Route::get('/posts', [App\Http\Controllers\Api\Public\Post\PostController::class, 'index']);
Route::get('/posts/slug/{slug}', [App\Http\Controllers\Api\Public\Post\PostController::class, 'showBySlug']);
Route::get('/posts/{id}', [App\Http\Controllers\Api\Public\Post\PostController::class, 'show']);
Route::get('/post-categories', [App\Http\Controllers\Api\Public\PostCategory\PostCategoryController::class, 'index']);
Route::get('/post-categories/slug/{slug}', [App\Http\Controllers\Api\Public\PostCategory\PostCategoryController::class, 'showBySlug']);
Route::get('/post-categories/{id}', [App\Http\Controllers\Api\Public\PostCategory\PostCategoryController::class, 'show']);
Route::get('/post-tags', [App\Http\Controllers\Api\Public\PostTag\PostTagController::class, 'index']);
Route::get('/post-tags/slug/{slug}', [App\Http\Controllers\Api\Public\PostTag\PostTagController::class, 'showBySlug']);
Route::get('/post-tags/{id}', [App\Http\Controllers\Api\Public\PostTag\PostTagController::class, 'show']);

// Public API - Contact (không cần authentication)
Route::post('/contacts', [PublicContactController::class, 'store']);

// Protected routes - cần authentication
Route::middleware(['auto.auth'])->group(function () {
    // User routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/change-password', [ApiUserController::class, 'changePassword']);

// Admin User API - cần role admin
Route::middleware(['auto.auth', 'role:admin'])->prefix('admin')->group(function () { 
    Route::apiResource('users', UserController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('roles', RoleController::class);
    
    // Image upload
    Route::post('images/upload', [App\Http\Controllers\Api\Core\File\FileController::class, 'upload']);
    
    Route::patch('users/toggle-status/{id}', [UserController::class, 'toggleStatus']);
    Route::get('users/statuses', [UserController::class, 'statuses']);
    Route::post('users/{id}/change-password', [UserController::class, 'changePassword']);
    Route::post('users/{id}/assign-roles', [UserController::class, 'assignRoles']);

    // Admin - Posts module
    Route::apiResource('posts', App\Http\Controllers\Api\Admin\Post\PostController::class);
    Route::apiResource('post-categories', App\Http\Controllers\Api\Admin\PostCategory\PostCategoryController::class);
    Route::apiResource('post-tags', App\Http\Controllers\Api\Admin\PostTag\PostTagController::class);
    
    // Admin - Contact module
    Route::apiResource('contacts', ContactController::class);
    
    // Contact additional routes - phải đặt sau apiResource
    Route::prefix('contacts')->group(function () {
        // Status management
        Route::patch('/{id}/status', [ContactController::class, 'updateStatus']);
        Route::patch('/{id}/mark-responded', [ContactController::class, 'markAsResponded']);
        Route::post('/bulk-update-status', [ContactController::class, 'bulkUpdateStatus']);
        
    });
});

// Public API - Enums (không cần authentication)
Route::prefix('enums')->group(function () {
    Route::get('/types', [EnumController::class, 'getTypes']);
    Route::get('/{type}', [EnumController::class, 'get']);
});

// Public API - File upload (không cần authentication)
Route::prefix('files')->group(function () {
    Route::post('/upload', [FileController::class, 'upload']);
    Route::post('/upload-multiple', [FileController::class, 'uploadMultiple']);
    Route::get('/info', [FileController::class, 'getInfo']);
    Route::get('/list', [FileController::class, 'list']);
    Route::get('/available-dates', [FileController::class, 'getAvailableDates']);
    Route::get('/supported-types', [FileController::class, 'getSupportedTypes']);
    Route::delete('/delete', [FileController::class, 'delete']);
});

// Admin routes for enum cache management
Route::prefix('admin')->group(function () {
    Route::delete('/enums/{type}/cache', [EnumController::class, 'clearCache']);
    Route::get('/enums/cache/all', [EnumController::class, 'clearAllCache']);
});