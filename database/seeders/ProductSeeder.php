<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\Warehouse;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'description' => 'iPhone 15 Pro Max với chip A17 Pro, camera 48MP, màn hình 6.7 inch',
                'price' => 29990000,
                'original_price' => 32990000,
                'sku' => 'IP15PM-256GB',
                'brand' => 'Apple',
                'weight' => 221,
                'dimensions' => ['length' => 159.9, 'width' => 76.7, 'height' => 8.25],
                'images' => ['iphone15-pro-max-1.jpg', 'iphone15-pro-max-2.jpg'],
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'description' => 'Samsung Galaxy S24 Ultra với S Pen, camera 200MP, màn hình 6.8 inch',
                'price' => 27990000,
                'original_price' => 29990000,
                'sku' => 'SS24U-512GB',
                'brand' => 'Samsung',
                'weight' => 232,
                'dimensions' => ['length' => 163.4, 'width' => 79.0, 'height' => 8.6],
                'images' => ['galaxy-s24-ultra-1.jpg', 'galaxy-s24-ultra-2.jpg'],
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'MacBook Pro 14 inch M3',
                'slug' => 'macbook-pro-14-m3',
                'description' => 'MacBook Pro 14 inch với chip M3, 16GB RAM, 512GB SSD',
                'price' => 45990000,
                'original_price' => 49990000,
                'sku' => 'MBP14-M3-512GB',
                'brand' => 'Apple',
                'weight' => 1600,
                'dimensions' => ['length' => 312.6, 'width' => 221.2, 'height' => 15.5],
                'images' => ['macbook-pro-14-1.jpg', 'macbook-pro-14-2.jpg'],
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'Dell XPS 13 Plus',
                'slug' => 'dell-xps-13-plus',
                'description' => 'Dell XPS 13 Plus với Intel Core i7, 16GB RAM, 512GB SSD',
                'price' => 35990000,
                'original_price' => 39990000,
                'sku' => 'DXPS13-16GB-512GB',
                'brand' => 'Dell',
                'weight' => 1200,
                'dimensions' => ['length' => 295.4, 'width' => 199.4, 'height' => 15.28],
                'images' => ['dell-xps-13-1.jpg', 'dell-xps-13-2.jpg'],
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name' => 'AirPods Pro 2',
                'slug' => 'airpods-pro-2',
                'description' => 'AirPods Pro 2 với chống ồn chủ động, âm thanh không gian',
                'price' => 5990000,
                'original_price' => 6990000,
                'sku' => 'APP2-WHITE',
                'brand' => 'Apple',
                'weight' => 5.3,
                'dimensions' => ['length' => 30.9, 'width' => 21.8, 'height' => 24.0],
                'images' => ['airpods-pro-2-1.jpg', 'airpods-pro-2-2.jpg'],
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'slug' => 'sony-wh-1000xm5',
                'description' => 'Sony WH-1000XM5 với chống ồn hàng đầu, pin 30 giờ',
                'price' => 7990000,
                'original_price' => 8990000,
                'sku' => 'SWH1000XM5-BLACK',
                'brand' => 'Sony',
                'weight' => 250,
                'dimensions' => ['length' => 248, 'width' => 168, 'height' => 74],
                'images' => ['sony-wh1000xm5-1.jpg', 'sony-wh1000xm5-2.jpg'],
                'status' => 'active',
                'featured' => false,
            ],
        ];

        $warehouses = Warehouse::all();

        foreach ($products as $productData) {
            $product = Product::create($productData);

            // Tạo tồn kho cho từng kho hàng
            foreach ($warehouses as $warehouse) {
                $quantity = rand(10, 100);
                $reserved = rand(0, 20);
                
                ProductInventory::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'quantity' => $quantity,
                    'reserved_quantity' => $reserved,
                    'available_quantity' => $quantity - $reserved,
                ]);
            }
        }
    }
}
