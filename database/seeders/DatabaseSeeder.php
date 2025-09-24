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
        // Chạy AuthSeeder trước
        $this->call([
            AuthSeeder::class,
            RolePermissionSeeder::class,
            RoleSeeder::class,
            PostCategorySeeder::class,
            PostTagSeeder::class,
            PostSeeder::class,
        ]);
    }
}
