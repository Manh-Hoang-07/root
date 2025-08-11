<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Variant;
use App\Models\Product;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variants = [
            // iPhone 15 Pro Max variants
            [
                'product_id' => 1, // iPhone 15 Pro Max
                'name' => 'iPhone 15 Pro Max 256GB Titan Tự Nhiên',
                'sku' => 'IP15PM-256GB-TITAN',
                'price' => 29990000,
                'sale_price' => 32990000,
                'quantity' => 50,
                'image' => 'iphone15-pro-max-titan-1.jpg',
                'status' => 'active',
            ],
            [
                'product_id' => 1, // iPhone 15 Pro Max
                'name' => 'iPhone 15 Pro Max 512GB Titan Tự Nhiên',
                'sku' => 'IP15PM-512GB-TITAN',
                'price' => 33990000,
                'sale_price' => 36990000,
                'quantity' => 30,
                'image' => 'iphone15-pro-max-titan-2.jpg',
                'status' => 'active',
            ],
            [
                'product_id' => 1, // iPhone 15 Pro Max
                'name' => 'iPhone 15 Pro Max 1TB Titan Tự Nhiên',
                'sku' => 'IP15PM-1TB-TITAN',
                'price' => 37990000,
                'sale_price' => 40990000,
                'quantity' => 20,
                'image' => 'iphone15-pro-max-titan-3.jpg',
                'status' => 'active',
            ],

            // Samsung Galaxy S24 Ultra variants
            [
                'product_id' => 2, // Samsung Galaxy S24 Ultra
                'name' => 'Samsung Galaxy S24 Ultra 256GB Titanium Gray',
                'sku' => 'SS24U-256GB-GRAY',
                'price' => 27990000,
                'sale_price' => 29990000,
                'quantity' => 45,
                'image' => 'galaxy-s24-ultra-gray-1.jpg',
                'status' => 'active',
            ],
            [
                'product_id' => 2, // Samsung Galaxy S24 Ultra
                'name' => 'Samsung Galaxy S24 Ultra 512GB Titanium Gray',
                'sku' => 'SS24U-512GB-GRAY',
                'price' => 30990000,
                'sale_price' => 32990000,
                'quantity' => 35,
                'image' => 'galaxy-s24-ultra-gray-2.jpg',
                'status' => 'active',
            ],

            // MacBook Pro 14 inch M3 variants
            [
                'product_id' => 3, // MacBook Pro 14 inch M3
                'name' => 'MacBook Pro 14 inch M3 16GB RAM 512GB SSD',
                'sku' => 'MBP14-M3-16GB-512GB',
                'price' => 45990000,
                'sale_price' => 49990000,
                'quantity' => 25,
                'image' => 'macbook-pro-14-m3-1.jpg',
                'status' => 'active',
            ],
            [
                'product_id' => 3, // MacBook Pro 14 inch M3
                'name' => 'MacBook Pro 14 inch M3 16GB RAM 1TB SSD',
                'sku' => 'MBP14-M3-16GB-1TB',
                'price' => 51990000,
                'sale_price' => 55990000,
                'quantity' => 20,
                'image' => 'macbook-pro-14-m3-2.jpg',
                'status' => 'active',
            ],

            // Dell XPS 13 Plus variants
            [
                'product_id' => 4, // Dell XPS 13 Plus
                'name' => 'Dell XPS 13 Plus Intel Core i7 16GB RAM 512GB SSD',
                'sku' => 'DXPS13-I7-16GB-512GB',
                'price' => 35990000,
                'sale_price' => 39990000,
                'quantity' => 30,
                'image' => 'dell-xps-13-plus-1.jpg',
                'status' => 'active',
            ],
            [
                'product_id' => 4, // Dell XPS 13 Plus
                'name' => 'Dell XPS 13 Plus Intel Core i7 32GB RAM 1TB SSD',
                'sku' => 'DXPS13-I7-32GB-1TB',
                'price' => 41990000,
                'sale_price' => 45990000,
                'quantity' => 15,
                'image' => 'dell-xps-13-plus-2.jpg',
                'status' => 'active',
            ],

            // AirPods Pro 2 variants
            [
                'product_id' => 5, // AirPods Pro 2
                'name' => 'AirPods Pro 2 Magsafe Charging Case',
                'sku' => 'APP2-MAGSAFE-WHITE',
                'price' => 5990000,
                'sale_price' => 6990000,
                'quantity' => 100,
                'image' => 'airpods-pro-2-white-1.jpg',
                'status' => 'active',
            ],
            [
                'product_id' => 5, // AirPods Pro 2
                'name' => 'AirPods Pro 2 Magsafe Charging Case - Space Gray',
                'sku' => 'APP2-MAGSAFE-GRAY',
                'price' => 5990000,
                'sale_price' => 6990000,
                'quantity' => 80,
                'image' => 'airpods-pro-2-gray-1.jpg',
                'status' => 'active',
            ],

            // Sony WH-1000XM5 variants
            [
                'product_id' => 6, // Sony WH-1000XM5
                'name' => 'Sony WH-1000XM5 Black',
                'sku' => 'SWH1000XM5-BLACK',
                'price' => 7990000,
                'sale_price' => 8990000,
                'quantity' => 60,
                'image' => 'sony-wh1000xm5-black-1.jpg',
                'status' => 'active',
            ],
            [
                'product_id' => 6, // Sony WH-1000XM5
                'name' => 'Sony WH-1000XM5 Silver',
                'sku' => 'SWH1000XM5-SILVER',
                'price' => 7990000,
                'sale_price' => 8990000,
                'quantity' => 40,
                'image' => 'sony-wh1000xm5-silver-1.jpg',
                'status' => 'active',
            ],
        ];

        foreach ($variants as $variant) {
            Variant::create($variant);
        }
    }
}
