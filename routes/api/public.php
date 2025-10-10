<?php

use App\Http\Controllers\Api\Core\Enum\EnumController;
use App\Http\Controllers\Api\Core\File\FileController;
use App\Http\Controllers\Api\Core\Auth\AuthController;
use App\Http\Controllers\Api\Public\Post\PostController;
use App\Http\Controllers\Api\Public\PostCategory\PostCategoryController;
use App\Http\Controllers\Api\Public\PostTag\PostTagController;
use App\Http\Controllers\Api\Public\Contact\ContactController;
use App\Http\Controllers\Api\Public\SystemConfig\SystemConfigController;
use Illuminate\Support\Facades\Route;

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
Route::apiResource('posts', PostController::class)->only(['index', 'show']);
Route::get('/posts/slug/{slug}', [PostController::class, 'showBySlug']);
Route::apiResource('post-categories', PostCategoryController::class)->only(['index', 'show']);
Route::get('/post-categories/slug/{slug}', [PostCategoryController::class, 'showBySlug']);
Route::apiResource('post-tags', PostTagController::class)->only(['index', 'show']);
Route::get('/post-tags/slug/{slug}', [PostTagController::class, 'showBySlug']);

// Public API - Contact module
Route::apiResource('contacts', ContactController::class)->only(['store']);

// Public API - System Config module
Route::prefix('config')->group(function () {
    Route::get('/groups', [SystemConfigController::class, 'getGroups']);
    Route::get('/key', [SystemConfigController::class, 'getByKey']);
    Route::get('/', [SystemConfigController::class, 'index']);
    Route::get('/{id}', [SystemConfigController::class, 'show']);
});
