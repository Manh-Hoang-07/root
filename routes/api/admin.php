<?php

use App\Http\Controllers\Api\Admin\Menu\MenuController;
use App\Http\Controllers\Api\Admin\User\UserController;
use App\Http\Controllers\Api\Core\Enum\EnumController;
use App\Http\Controllers\Api\Admin\Permission\PermissionController;
use App\Http\Controllers\Api\Admin\Role\RoleController;
use App\Http\Controllers\Api\Admin\Contact\ContactController;
use App\Http\Controllers\Api\Admin\Post\PostController;
use App\Http\Controllers\Api\Admin\PostCategory\PostCategoryController;
use App\Http\Controllers\Api\Admin\PostTag\PostTagController;
use App\Http\Controllers\Api\Admin\SystemConfig\SystemConfigController;
use App\Http\Controllers\Api\Admin\SystemConfig\ConfigAuditController;
use Illuminate\Support\Facades\Route;

// Admin API
Route::middleware(['auto.auth'])->prefix('admin')->group(function () {
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
    Route::apiResource('menus', MenuController::class);
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

    // Admin - System Config module - Special routes BEFORE apiResource
    Route::prefix('system-configs')->group(function () {
        // Special operations
        Route::get('/groups', [SystemConfigController::class, 'getGroups']);
        Route::get('/group', [SystemConfigController::class, 'getByGroup']);
        Route::get('/group/{group}', [SystemConfigController::class, 'getByGroup']);
        Route::get('/key', [SystemConfigController::class, 'getByKey']);
        Route::post('/bulk-update', [SystemConfigController::class, 'bulkUpdate']);
        Route::post('/clear-cache', [SystemConfigController::class, 'clearCache']);
    });
    
    // Admin - System Config module - RESTful routes
    Route::apiResource('system-configs', SystemConfigController::class);

    // Admin - Config Audit module
    Route::prefix('system-configs-audit')->group(function () {
        Route::get('/logs', [ConfigAuditController::class, 'index']);
        Route::get('/export', [ConfigAuditController::class, 'export']);
    });
});
