<?php

use App\Http\Controllers\Api\Core\Auth\AuthController;
use App\Http\Controllers\Api\User\User\UserController;
use Illuminate\Support\Facades\Route;

// User API
Route::middleware(['auto.auth'])->group(function () {
    // User routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
});
