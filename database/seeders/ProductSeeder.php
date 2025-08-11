<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Inventory;
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
                'short_description' => 'iPhone 15 Pro Max - Điện thoại cao cấp nhất của Apple',
                'price' => 29990000,
                'sale_price' => 32990000,
                'image' => 'iphone15-pro-max-1.jpg',
                'weight' => 221,
                'length' => 159.9,
                'width' => 76.7,
                'height' => 8.25,
                'brand_id' => 1, // Apple
                'status' => 'active',
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'description' => 'Samsung Galaxy S24 Ultra với S Pen, camera 200MP, màn hình 6.8 inch',
                'short_description' => 'Galaxy S24 Ultra - Flagship Android với S Pen tích hợp',
                'price' => 27990000,
                'sale_price' => 29990000,
                'image' => 'galaxy-s24-ultra-1.jpg',
                'weight' => 232,
                'length' => 163.4,
                'width' => 79.0,
                'height' => 8.6,
                'brand_id' => 2, // Samsung
                'status' => 'active',
            ],
            [
                'name' => 'MacBook Pro 14 inch M3',
                'slug' => 'macbook-pro-14-m3',
                'description' => 'MacBook Pro 14 inch với chip M3, 16GB RAM, 512GB SSD',
                'short_description' => 'MacBook Pro 14 inch với chip M3 mạnh mẽ',
                'price' => 45990000,
                'sale_price' => 49990000,
                'image' => 'macbook-pro-14-1.jpg',
                'weight' => 1600,
                'length' => 312.6,
                'width' => 221.2,
                'height' => 15.5,
                'brand_id' => 1, // Apple
                'status' => 'active',
            ],
            [
                'name' => 'Dell XPS 13 Plus',
                'slug' => 'dell-xps-13-plus',
                'description' => 'Dell XPS 13 Plus với Intel Core i7, 16GB RAM, 512GB SSD',
                'short_description' => 'Dell XPS 13 Plus - Laptop cao cấp cho doanh nhân',
                'price' => 35990000,
                'sale_price' => 39990000,
                'image' => 'dell-xps-13-1.jpg',
                'weight' => 1200,
                'length' => 295.4,
                'width' => 199.4,
                'height' => 15.28,
                'brand_id' => 3, // Dell
                'status' => 'active',
            ],
            [
                'name' => 'AirPods Pro 2',
                'slug' => 'airpods-pro-2',
                'description' => 'AirPods Pro 2 với chống ồn chủ động, âm thanh không gian',
                'short_description' => 'AirPods Pro 2 - Tai nghe không dây cao cấp',
                'price' => 5990000,
                'sale_price' => 6990000,
                'image' => 'airpods-pro-2-1.jpg',
                'weight' => 5.3,
                'length' => 30.9,
                'width' => 21.8,
                'height' => 24.0,
                'brand_id' => 1, // Apple
                'status' => 'active',
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'slug' => 'sony-wh-1000xm5',
                'description' => 'Sony WH-1000XM5 với chống ồn hàng đầu, pin 30 giờ',
                'short_description' => 'Sony WH-1000XM5 - Tai nghe chống ồn tốt nhất',
                'price' => 7990000,
                'sale_price' => 8990000,
                'image' => 'sony-wh1000xm5-1.jpg',
                'weight' => 250,
                'length' => 248,
                'width' => 168,
                'height' => 74,
                'brand_id' => 4, // Sony
                'status' => 'active',
            ],
        ];

        $warehouses = Warehouse::all();

        foreach ($products as $productData) {
            $product = Product::create($productData);

            // Tạo tồn kho cho từng kho hàng
            foreach ($warehouses as $warehouse) {
                $quantity = rand(10, 100);
                $reserved = rand(0, 20);
                
                Inventory::create([
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
