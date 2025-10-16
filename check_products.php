<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

echo "Checking products in database...\n";

try {
    $products = App\Models\Product::all(['id', 'name', 'status']);
    echo "Found " . $products->count() . " products:\n";
    
    foreach($products as $p) {
        echo "ID: " . $p->id . " - Name: " . $p->name . " - Status: " . $p->status . "\n";
    }
    
    // Check specifically for ID 2
    $product2 = App\Models\Product::find(2);
    if ($product2) {
        echo "\nProduct ID 2 exists: " . $product2->name . " (Status: " . $product2->status . ")\n";
    } else {
        echo "\nProduct ID 2 does NOT exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
