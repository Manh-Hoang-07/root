<?php

namespace Database\Seeders;

use App\Enums\BasicStatus;
use Illuminate\Database\Seeder;
use App\Models\PostCategory;

class PostCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Root categories
        $roots = [
            ['name' => 'Tin tức', 'slug' => 'tin-tuc'],
            ['name' => 'Khuyến mãi', 'slug' => 'khuyen-mai'],
            ['name' => 'Thủ thuật', 'slug' => 'thu-thuat'],
            ['name' => 'Hướng dẫn', 'slug' => 'huong-dan'],
        ];

        $rootIds = [];
        foreach ($roots as $data) {
            $root = PostCategory::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'description' => null,
                    'status' => BasicStatus::Active,
                    'sort_order' => 0,
                ])
            );
            $rootIds[] = $root->id;
        }

        // Child categories
        foreach ($rootIds as $parentId) {
            PostCategory::factory()->count(3)->create([
                'parent_id' => $parentId,
                'status' => BasicStatus::Active,
            ]);
        }
    }
}
