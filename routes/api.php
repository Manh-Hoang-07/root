<?php

use Illuminate\Support\Facades\Route;

// CORS preflight route
Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization, X-XSRF-TOKEN')
        ->header('Access-Control-Allow-Credentials', 'true');
})->where('any', '.*');

// Include route files
require __DIR__ . '/api/public.php';
require __DIR__ . '/api/user.php';
require __DIR__ . '/api/admin.php';
