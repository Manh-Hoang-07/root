<?php

// Test script để kiểm tra API upload ảnh
require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Core\ImageController;

// Tạo request giả lập
$request = new Request();
$request->files->set('image', new \Illuminate\Http\UploadedFile(
    __DIR__ . '/public/favicon.ico', // Sử dụng favicon.ico làm test
    'test.jpg',
    'image/jpeg',
    null,
    true
));

// Tạo controller instance
$controller = new ImageController();

// Test upload
try {
    $response = $controller->upload($request);
    echo "Response: " . json_encode($response->getData(), JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 