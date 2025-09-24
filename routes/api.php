<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\User\UserController;
use App\Http\Controllers\Api\Core\Enum\EnumController;
use App\Http\Controllers\Api\Core\File\FileController;
use App\Http\Controllers\Api\Admin\Permission\PermissionController;
use App\Http\Controllers\Api\Admin\Role\RoleController;
use App\Http\Controllers\Api\Core\Auth\AuthController;
use App\Http\Controllers\Api\User\User\UserController as ApiUserController;
use App\Http\Controllers\Api\Public\Contact\ContactController as PublicContactController;
use App\Http\Controllers\Api\Admin\Contact\ContactController;
use App\Http\Controllers\Api\Public\Post\PostController as PublicPostController;
use App\Http\Controllers\Api\Public\PostCategory\PostCategoryController as PublicPostCategoryController;
use App\Http\Controllers\Api\Public\PostTag\PostTagController as PublicPostTagController;
use App\Http\Controllers\Api\Admin\Post\PostController;
use App\Http\Controllers\Api\Admin\PostCategory\PostCategoryController;
use App\Http\Controllers\Api\Admin\PostTag\PostTagController;

// CORS preflight route
Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization, X-XSRF-TOKEN')
        ->header('Access-Control-Allow-Credentials', 'true');
})->where('any', '.*');

// Public API - Auth module
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public API - Enum
Route::prefix('enums')->group(function () {
    Route::get('/types', [EnumController::class, 'getTypes']);
    Route::get('/{type}', [EnumController::class, 'get']);
});

// Public API - File upload
Route::prefix('files')->group(function () {
    Route::post('/upload', [FileController::class, 'upload']);
    Route::post('/upload-multiple', [FileController::class, 'uploadMultiple']);
    Route::delete('/delete', [FileController::class, 'delete']);
});

// Public API - Post module
Route::apiResource('posts', UserController::class)->only(['index', 'show']);
Route::get('/posts/slug/{slug}', [PublicPostController::class, 'showBySlug']);
Route::apiResource('post-categories', PublicPostCategoryController::class)->only(['index', 'show']);
Route::get('/post-categories/slug/{slug}', [PublicPostCategoryController::class, 'showBySlug']);
Route::apiResource('post-tags', PublicPostTagController::class)->only(['index', 'show']);
Route::get('/post-tags/slug/{slug}', [PublicPostTagController::class, 'showBySlug']);

// Public API - Contact module
Route::apiResource('contacts', PublicPostTagController::class)->only(['store']);

// User API
Route::middleware(['auto.auth'])->group(function () {
    // User routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/change-password', [ApiUserController::class, 'changePassword']);
});

// Admin API
Route::middleware(['auto.auth', 'role:admin'])->prefix('admin')->group(function () {
    // Admin routes for enum cache management
    Route::delete('/enums/cache/{type}', [EnumController::class, 'clearCache']);
    Route::get('/enums/cache/all', [EnumController::class, 'clearAllCache']);

    // Admin - Common module
    Route::apiResource('users', UserController::class);
    Route::patch('users/toggle-status/{id}', [UserController::class, 'toggleStatus']);
    Route::post('users/change-password/{id}', [UserController::class, 'changePassword']);
    Route::post('users/assign-roles/{id}', [UserController::class, 'assignRoles']);

    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('roles', RoleController::class);

    // Admin - Posts module
    Route::apiResource('posts', PostController::class);
    Route::apiResource('post-categories', PostCategoryController::class);
    Route::apiResource('post-tags', PostTagController::class);
    
    // Admin - Contact module
    Route::apiResource('contacts', ContactController::class);
    
    // Contact additional routes - phải đặt sau apiResource
    Route::prefix('contacts')->group(function () {
        // Status management
        Route::patch('/status/{id}', [ContactController::class, 'updateStatus']);
        Route::patch('/mark-responded/{id}', [ContactController::class, 'markAsResponded']);
        Route::post('/bulk-update-status', [ContactController::class, 'bulkUpdateStatus']);
    });
});