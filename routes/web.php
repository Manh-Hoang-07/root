<?php

use Illuminate\Support\Facades\Route;

// Serve Vue SPA for all routes except API
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');
