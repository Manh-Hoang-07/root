<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Test route để kiểm tra ảnh
Route::get('/test-image', function () {
    $testImage = '/storage/images/4Nt5QqOmEY3OQJamU4PBTVVgENrlf8FZwqppTf2L.png';
    return view('test-image', compact('testImage'));
});

// Test route để kiểm tra ảnh mới upload
Route::get('/test-upload', function () {
    $files = Storage::files('public/images');
    $latestFile = end($files);
    $testImage = Storage::url($latestFile);
    return view('test-image', compact('testImage'));
});




// Serve Vue SPA for all routes except API and Vite assets
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api|@vite|resources).*$');
