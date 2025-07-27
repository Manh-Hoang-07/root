<?php

require_once 'vendor/autoload.php';

use App\Models\User;

echo "=== Debug Authentication ===\n";

// Test 1: Kiểm tra user admin
$adminUser = User::where('email', 'admin@example.com')->first();
if ($adminUser) {
    echo "✅ Admin user found\n";
    echo "ID: " . $adminUser->id . "\n";
    echo "Email: " . $adminUser->email . "\n";
    echo "Has admin role: " . ($adminUser->hasRole('admin') ? 'Yes' : 'No') . "\n";
    echo "All roles: " . implode(', ', $adminUser->getRoleNames()->toArray()) . "\n";
    echo "All permissions: " . implode(', ', $adminUser->getAllPermissions()->pluck('name')->toArray()) . "\n\n";
} else {
    echo "❌ Admin user not found\n\n";
}

// Test 2: Kiểm tra user thường
$normalUser = User::where('email', 'user@example.com')->first();
if ($normalUser) {
    echo "✅ Normal user found\n";
    echo "ID: " . $normalUser->id . "\n";
    echo "Email: " . $normalUser->email . "\n";
    echo "Has admin role: " . ($normalUser->hasRole('admin') ? 'Yes' : 'No') . "\n";
    echo "All roles: " . implode(', ', $normalUser->getRoleNames()->toArray()) . "\n";
    echo "All permissions: " . implode(', ', $normalUser->getAllPermissions()->pluck('name')->toArray()) . "\n\n";
} else {
    echo "❌ Normal user not found\n\n";
}

// Test 3: Kiểm tra tất cả users
echo "=== All Users ===\n";
$allUsers = User::all();
foreach ($allUsers as $user) {
    echo "ID: {$user->id}, Email: {$user->email}, Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
}

echo "\n=== Debug completed ===\n"; 