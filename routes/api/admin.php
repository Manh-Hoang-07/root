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
use App\Http\Controllers\Api\Admin\NotificationTemplate\NotificationTemplateController;
use App\Http\Controllers\Api\Admin\Product\ProductController;
use App\Http\Controllers\Api\Admin\Product\ProductCategoryController;
use App\Http\Controllers\Api\Admin\Product\ProductVariantController;
use App\Http\Controllers\Api\Admin\Product\ProductAttributeController;
use App\Http\Controllers\Api\Admin\Order\OrderController;
use Illuminate\Support\Facades\Route;

// Admin API
Route::middleware(['auto.auth'])->prefix('admin')->group(function () {
    // Admin routes for enum cache management
    Route::delete('/enums/cache/{type}', [EnumController::class, 'clearCache']);
    Route::get('/enums/cache/all', [EnumController::class, 'clearAllCache']);

    // Admin - Common module
    Route::apiResource('users', UserController::class);
    Route::prefix('users')->group(function () {
        Route::patch('/status/{id}', [UserController::class, 'updateStatus']);
        Route::post('/change-password/{id}', [UserController::class, 'changePassword']);
        Route::post('/assign-roles/{id}', [UserController::class, 'assignRoles']);
    });

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

    // Admin - Notification Template module
    Route::apiResource('notification-templates', NotificationTemplateController::class);

    // Admin - E-commerce module
    Route::apiResource('products', ProductController::class);
    Route::prefix('products')->group(function () {
        Route::patch('/status/{id}', [ProductController::class, 'updateStatus']);
        Route::patch('/toggle-featured/{id}', [ProductController::class, 'toggleFeatured']);
    });

    Route::apiResource('product-categories', ProductCategoryController::class);
    Route::prefix('product-categories')->group(function () {
        Route::get('/tree', [ProductCategoryController::class, 'tree']);
        Route::get('/products/{id}', [ProductCategoryController::class, 'products']);
    });

    // Product Variants
    Route::apiResource('product-variants', ProductVariantController::class);
    Route::prefix('product-variants')->group(function () {
        Route::patch('/status/{id}', [ProductVariantController::class, 'updateStatus']);
        Route::get('/product/{productId}', [ProductVariantController::class, 'variants']);
    });

    // Product Attributes
    Route::apiResource('product-attributes', ProductAttributeController::class);
    Route::prefix('product-attributes')->group(function () {
        Route::patch('/status/{id}', [ProductAttributeController::class, 'updateStatus']);
    });

    Route::apiResource('orders', OrderController::class);
    Route::prefix('orders')->group(function () {
        Route::patch('/status/{id}', [OrderController::class, 'updateStatus']);
        Route::patch('/payment-status/{id}', [OrderController::class, 'updatePaymentStatus']);
        Route::patch('/shipping-status/{id}', [OrderController::class, 'updateShippingStatus']);
        Route::post('/{id}/items', [OrderController::class, 'addItem']);
        Route::patch('/{orderId}/items/{itemId}', [OrderController::class, 'updateItem']);
        Route::delete('/{orderId}/items/{itemId}', [OrderController::class, 'removeItem']);
        Route::post('/{id}/recalculate', [OrderController::class, 'recalculate']);
        Route::post('/{id}/confirm', [OrderController::class, 'confirm']);
        Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
        Route::post('/bulk-update-status', [OrderController::class, 'bulkUpdateStatus']);
    });
});
