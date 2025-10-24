<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Enums\BasicStatus;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main categories
        $categories = [
            [
                'name' => 'Điện thoại',
                'slug' => 'dien-thoai',
                'description' => 'Các loại điện thoại thông minh, điện thoại cơ bản',
                'parent_id' => null,
                'image' => 'categories/dien-thoai.jpg',
                'icon' => 'fas fa-mobile-alt',
                'status' => BasicStatus::Active->value,
                'sort_order' => 1,
                'meta_title' => 'Điện thoại thông minh',
                'meta_description' => 'Mua bán điện thoại thông minh chính hãng',
            ],
            [
                'name' => 'Laptop',
                'slug' => 'laptop',
                'description' => 'Laptop gaming, laptop văn phòng, workstation',
                'parent_id' => null,
                'image' => 'categories/laptop.jpg',
                'icon' => 'fas fa-laptop',
                'status' => BasicStatus::Active->value,
                'sort_order' => 2,
                'meta_title' => 'Laptop chính hãng',
                'meta_description' => 'Mua laptop giá tốt, bảo hành uy tín',
            ],
            [
                'name' => 'Tablet',
                'slug' => 'tablet',
                'description' => 'Tablet Android, iPad, tablet Windows',
                'parent_id' => null,
                'image' => 'categories/tablet.jpg',
                'icon' => 'fas fa-tablet-alt',
                'status' => BasicStatus::Active->value,
                'sort_order' => 3,
                'meta_title' => 'Tablet giá tốt',
                'meta_description' => 'Mua tablet chính hãng với giá tốt nhất',
            ],
            [
                'name' => 'Phụ kiện',
                'slug' => 'phu-kien',
                'description' => 'Sạc, cáp, tai nghe, ốp lưng, bàn phím',
                'parent_id' => null,
                'image' => 'categories/phu-kien.jpg',
                'icon' => 'fas fa-headphones',
                'status' => BasicStatus::Active->value,
                'sort_order' => 4,
                'meta_title' => 'Phụ kiện điện tử',
                'meta_description' => 'Phụ kiện chính hãng cho điện thoại, laptop',
            ],
            [
                'name' => 'Smartwatch',
                'slug' => 'smartwatch',
                'description' => 'Đồng hồ thông minh, fitness tracker',
                'parent_id' => null,
                'image' => 'categories/smartwatch.jpg',
                'icon' => 'fas fa-clock',
                'status' => BasicStatus::Active->value,
                'sort_order' => 5,
                'meta_title' => 'Đồng hồ thông minh',
                'meta_description' => 'Đồng hồ thông minh chính hãng',
            ],
        ];

        // Create main categories
        $createdCategories = [];
        foreach ($categories as $category) {
            $createdCategory = ProductCategory::firstOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, [
                    'created_user_id' => 1,
                    'updated_user_id' => 1,
                ])
            );
            $createdCategories[$category['slug']] = $createdCategory;
        }

        // Subcategories for Điện thoại
        $phoneSubcategories = [
            [
                'name' => 'iPhone',
                'slug' => 'iphone',
                'description' => 'iPhone 15, iPhone 14, iPhone 13',
                'parent_id' => $createdCategories['dien-thoai']->id,
                'status' => BasicStatus::Active->value,
                'sort_order' => 1,
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'description' => 'Galaxy S, Galaxy Note, Galaxy A',
                'parent_id' => $createdCategories['dien-thoai']->id,
                'status' => BasicStatus::Active->value,
                'sort_order' => 2,
            ],
            [
                'name' => 'Xiaomi',
                'slug' => 'xiaomi',
                'description' => 'Mi, Redmi, Poco',
                'parent_id' => $createdCategories['dien-thoai']->id,
                'status' => BasicStatus::Active->value,
                'sort_order' => 3,
            ],
            [
                'name' => 'Oppo',
                'slug' => 'oppo',
                'description' => 'Find, Reno, A series',
                'parent_id' => $createdCategories['dien-thoai']->id,
                'status' => BasicStatus::Active->value,
                'sort_order' => 4,
            ],
        ];

        // Create phone subcategories
        foreach ($phoneSubcategories as $subcategory) {
            ProductCategory::firstOrCreate(
                ['slug' => $subcategory['slug']],
                array_merge($subcategory, [
                    'created_user_id' => 1,
                    'updated_user_id' => 1,
                ])
            );
        }

        // Subcategories for Laptop
        $laptopSubcategories = [
            [
                'name' => 'Laptop Gaming',
                'slug' => 'laptop-gaming',
                'description' => 'ASUS ROG, MSI, Alienware',
                'parent_id' => $createdCategories['laptop']->id,
                'status' => BasicStatus::Active->value,
                'sort_order' => 1,
            ],
            [
                'name' => 'Laptop Văn phòng',
                'slug' => 'laptop-van-phong',
                'description' => 'Dell, HP, Lenovo',
                'parent_id' => $createdCategories['laptop']->id,
                'status' => BasicStatus::Active->value,
                'sort_order' => 2,
            ],
            [
                'name' => 'Macbook',
                'slug' => 'macbook',
                'description' => 'MacBook Air, MacBook Pro',
                'parent_id' => $createdCategories['laptop']->id,
                'status' => BasicStatus::Active->value,
                'sort_order' => 3,
            ],
        ];

        // Create laptop subcategories
        foreach ($laptopSubcategories as $subcategory) {
            ProductCategory::firstOrCreate(
                ['slug' => $subcategory['slug']],
                array_merge($subcategory, [
                    'created_user_id' => 1,
                    'updated_user_id' => 1,
                ])
            );
        }

        // Subcategories for Phụ kiện
        $accessorySubcategories = [
            [
                'name' => 'Sạc & Cáp',
                'slug' => 'sac-cap',
                'description' => 'Sạc nhanh, sạc dự phòng, cáp sạc',
                'parent_id' => $createdCategories['phu-kien']->id,
                'status' => BasicStatus::Active->value,
                'sort_order' => 1,
            ],
            [
                'name' => 'Tai nghe',
                'slug' => 'tai-nghe',
                'description' => 'Tai nghe true wireless, tai nghe có dây',
                'parent_id' => $createdCategories['phu-kien']->id,
                'status' => BasicStatus::Active->value,
                'sort_order' => 2,
            ],
            [
                'name' => 'Ốp lưng & Bao da',
                'slug' => 'op-lung-bao-da',
                'description' => 'Ốp lưng điện thoại, bao da tablet',
                'parent_id' => $createdCategories['phu-kien']->id,
                'status' => BasicStatus::Active->value,
                'sort_order' => 3,
            ],
        ];

        // Create accessory subcategories
        foreach ($accessorySubcategories as $subcategory) {
            ProductCategory::firstOrCreate(
                ['slug' => $subcategory['slug']],
                array_merge($subcategory, [
                    'created_user_id' => 1,
                    'updated_user_id' => 1,
                ])
            );
        }

        $this->command->info('ProductCategorySeeder completed successfully!');
    }
}