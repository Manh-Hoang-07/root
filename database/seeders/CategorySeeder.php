<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Điện thoại',
                'slug' => 'dien-thoai',
                'description' => 'Các loại điện thoại di động, smartphone từ các thương hiệu uy tín',
                'image' => 'phone-category.jpg',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Laptop',
                'slug' => 'laptop',
                'description' => 'Máy tính xách tay, notebook với nhiều cấu hình khác nhau',
                'image' => 'laptop-category.jpg',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Máy tính bảng',
                'slug' => 'may-tinh-bang',
                'description' => 'Tablet, iPad và các thiết bị màn hình cảm ứng',
                'image' => 'tablet-category.jpg',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Phụ kiện',
                'slug' => 'phu-kien',
                'description' => 'Tai nghe, sạc, ốp lưng và các phụ kiện khác',
                'image' => 'accessories-category.jpg',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Điện thoại Android',
                'slug' => 'dien-thoai-android',
                'description' => 'Smartphone chạy hệ điều hành Android',
                'image' => 'android-phone.jpg',
                'parent_id' => 1, // Điện thoại
                'status' => 'active',
            ],
            [
                'name' => 'iPhone',
                'slug' => 'iphone',
                'description' => 'Điện thoại iPhone của Apple',
                'image' => 'iphone-category.jpg',
                'parent_id' => 1, // Điện thoại
                'status' => 'active',
            ],
            [
                'name' => 'Laptop Gaming',
                'slug' => 'laptop-gaming',
                'description' => 'Laptop chuyên game với card đồ họa mạnh',
                'image' => 'gaming-laptop.jpg',
                'parent_id' => 2, // Laptop
                'status' => 'active',
            ],
            [
                'name' => 'Laptop Văn phòng',
                'slug' => 'laptop-van-phong',
                'description' => 'Laptop phù hợp cho công việc văn phòng',
                'image' => 'office-laptop.jpg',
                'parent_id' => 2, // Laptop
                'status' => 'active',
            ],
            [
                'name' => 'Tai nghe',
                'slug' => 'tai-nghe',
                'description' => 'Tai nghe có dây, không dây, bluetooth',
                'image' => 'headphones.jpg',
                'parent_id' => 4, // Phụ kiện
                'status' => 'active',
            ],
            [
                'name' => 'Sạc & Cáp',
                'slug' => 'sac-cap',
                'description' => 'Sạc điện thoại, cáp USB, adapter',
                'image' => 'charger-category.jpg',
                'parent_id' => 4, // Phụ kiện
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
