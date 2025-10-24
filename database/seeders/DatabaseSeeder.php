<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Chạy PermissionSeeder trước
            PermissionSeeder::class,
            
            // 2. Tạo users
            AuthSeeder::class,
            
            // 3. Tạo roles và gán quyền
            RoleSeeder::class,
            
            // 4. Seeder sản phẩm
            ProductCategorySeeder::class,
            ProductAttributeSeeder::class,
            ProductSeeder::class,
            
            // 5. Các seeder khác
            SystemConfigSeeder::class,
            MenuSeeder::class,
            NotificationTemplateSeeder::class,
            ConfigAuditLogSeeder::class,
            PostCategorySeeder::class,
            PostTagSeeder::class,
            PostSeeder::class,
            ContactSeeder::class,
        ]);
    }
}
