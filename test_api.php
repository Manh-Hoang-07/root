<?php

// Test script để kiểm tra API authentication
$baseUrl = 'http://127.0.0.1:8000/api';

echo "Testing API Authentication...\n\n";

// Test 1: Login
echo "1. Testing login...\n";
$loginData = [
    'email' => 'admin@example.com',
    'password' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

// Extract cookies from response
preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
$cookies = [];
foreach($matches[1] as $match) {
    $cookies[] = $match;
}

echo "Cookies found:\n";
foreach($cookies as $cookie) {
    echo "- $cookie\n";
}

echo "\n2. Testing /me endpoint...\n";

// Test 2: Get user info
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/me');
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Add cookies if found
if (!empty($cookies)) {
    $cookieString = implode('; ', $cookies);
    curl_setopt($ch, CURLOPT_COOKIE, $cookieString);
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

echo "Test completed!\n"; 