<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;

// Test 1: Đăng nhập admin
echo "=== Test 1: Đăng nhập admin ===\n";

$request = new Request();
$request->merge([
    'email' => 'admin@example.com',
    'password' => 'password'
]);

$authController = new AuthController();
$response = $authController->login($request);
$data = json_decode($response->getContent(), true);

if ($data['success']) {
    echo "✅ Admin đăng nhập thành công\n";
    echo "Role: " . $data['data']['user']['role'] . "\n";
    echo "Permissions: " . implode(', ', $data['data']['user']['permissions']) . "\n";
    echo "Token: " . substr($data['data']['token'], 0, 20) . "...\n\n";
} else {
    echo "❌ Admin đăng nhập thất bại: " . $data['message'] . "\n\n";
}

// Test 2: Đăng nhập user thường
echo "=== Test 2: Đăng nhập user thường ===\n";

$request = new Request();
$request->merge([
    'email' => 'user@example.com',
    'password' => 'password'
]);

$response = $authController->login($request);
$data = json_decode($response->getContent(), true);

if ($data['success']) {
    echo "✅ User đăng nhập thành công\n";
    echo "Role: " . $data['data']['user']['role'] . "\n";
    echo "Permissions: " . implode(', ', $data['data']['user']['permissions']) . "\n";
    echo "Token: " . substr($data['data']['token'], 0, 20) . "...\n\n";
} else {
    echo "❌ User đăng nhập thất bại: " . $data['message'] . "\n\n";
}

// Test 3: Kiểm tra roles và permissions trong database
echo "=== Test 3: Kiểm tra database ===\n";

$adminUser = User::where('email', 'admin@example.com')->first();
if ($adminUser) {
    echo "Admin user found\n";
    echo "Has admin role: " . ($adminUser->hasRole('admin') ? 'Yes' : 'No') . "\n";
    echo "Has manage_users permission: " . ($adminUser->can('manage_users') ? 'Yes' : 'No') . "\n";
    echo "All permissions: " . implode(', ', $adminUser->getAllPermissions()->pluck('name')->toArray()) . "\n\n";
}

$normalUser = User::where('email', 'user@example.com')->first();
if ($normalUser) {
    echo "Normal user found\n";
    echo "Has admin role: " . ($normalUser->hasRole('admin') ? 'Yes' : 'No') . "\n";
    echo "Has manage_users permission: " . ($normalUser->can('manage_users') ? 'Yes' : 'No') . "\n";
    echo "All permissions: " . implode(', ', $normalUser->getAllPermissions()->pluck('name')->toArray()) . "\n\n";
}

echo "=== Test hoàn thành ===\n"; 