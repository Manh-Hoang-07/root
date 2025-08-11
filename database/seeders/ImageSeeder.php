<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Image;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images = [
            // Product images
            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 1, // iPhone 15 Pro Max
                'url' => 'products/iphone15-pro-max-1.jpg',
            ],
            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 1, // iPhone 15 Pro Max
                'url' => 'products/iphone15-pro-max-2.jpg',
            ],
            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 1, // iPhone 15 Pro Max
                'url' => 'products/iphone15-pro-max-3.jpg',
            ],

            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 2, // Samsung Galaxy S24 Ultra
                'url' => 'products/galaxy-s24-ultra-1.jpg',
            ],
            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 2, // Samsung Galaxy S24 Ultra
                'url' => 'products/galaxy-s24-ultra-2.jpg',
            ],

            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 3, // MacBook Pro 14 inch M3
                'url' => 'products/macbook-pro-14-1.jpg',
            ],
            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 3, // MacBook Pro 14 inch M3
                'url' => 'products/macbook-pro-14-2.jpg',
            ],

            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 4, // Dell XPS 13 Plus
                'url' => 'products/dell-xps-13-1.jpg',
            ],
            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 4, // Dell XPS 13 Plus
                'url' => 'products/dell-xps-13-2.jpg',
            ],

            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 5, // AirPods Pro 2
                'url' => 'products/airpods-pro-2-1.jpg',
            ],
            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 5, // AirPods Pro 2
                'url' => 'products/airpods-pro-2-2.jpg',
            ],

            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 6, // Sony WH-1000XM5
                'url' => 'products/sony-wh1000xm5-1.jpg',
            ],
            [
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => 6, // Sony WH-1000XM5
                'url' => 'products/sony-wh1000xm5-2.jpg',
            ],

            // Category images
            [
                'imageable_type' => 'App\Models\Category',
                'imageable_id' => 1, // Điện thoại
                'url' => 'categories/phone-category.jpg',
            ],
            [
                'imageable_type' => 'App\Models\Category',
                'imageable_id' => 2, // Laptop
                'url' => 'categories/laptop-category.jpg',
            ],
            [
                'imageable_type' => 'App\Models\Category',
                'imageable_id' => 3, // Máy tính bảng
                'url' => 'categories/tablet-category.jpg',
            ],
            [
                'imageable_type' => 'App\Models\Category',
                'imageable_id' => 4, // Phụ kiện
                'url' => 'categories/accessories-category.jpg',
            ],

            // Brand images
            [
                'imageable_type' => 'App\Models\Brand',
                'imageable_id' => 1, // Apple
                'url' => 'brands/apple-logo.png',
            ],
            [
                'imageable_type' => 'App\Models\Brand',
                'imageable_id' => 2, // Samsung
                'url' => 'brands/samsung-logo.png',
            ],
            [
                'imageable_type' => 'App\Models\Brand',
                'imageable_id' => 3, // Dell
                'url' => 'brands/dell-logo.png',
            ],
            [
                'imageable_type' => 'App\Models\Brand',
                'imageable_id' => 4, // Sony
                'url' => 'brands/sony-logo.png',
            ],
            [
                'imageable_type' => 'App\Models\Brand',
                'imageable_id' => 5, // Microsoft
                'url' => 'brands/microsoft-logo.png',
            ],
            [
                'imageable_type' => 'App\Models\Brand',
                'imageable_id' => 6, // HP
                'url' => 'brands/hp-logo.png',
            ],
            [
                'imageable_type' => 'App\Models\Brand',
                'imageable_id' => 7, // Lenovo
                'url' => 'brands/lenovo-logo.png',
            ],
            [
                'imageable_type' => 'App\Models\Brand',
                'imageable_id' => 8, // Asus
                'url' => 'brands/asus-logo.png',
            ],
        ];

        foreach ($images as $image) {
            Image::create($image);
        }
    }
}
