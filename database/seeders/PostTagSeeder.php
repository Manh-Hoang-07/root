<?php

namespace Database\Seeders;

use App\Enums\BasicStatus;
use Illuminate\Database\Seeder;
use App\Models\PostTag;

class PostTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Ưu đãi', 'slug' => 'uu-dai'],
            ['name' => 'Sự kiện', 'slug' => 'su-kien'],
            ['name' => 'Mẹo vặt', 'slug' => 'meo-vat'],
            ['name' => 'Mua sắm', 'slug' => 'mua-sam'],
            ['name' => 'Công nghệ', 'slug' => 'cong-nghe'],
        ];

        foreach ($tags as $data) {
            PostTag::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'description' => null,
                    'status' => BasicStatus::Active,
                ])
            );
        }

        // Random tags
        PostTag::factory()->count(10)->create([
            'status' => BasicStatus::Active,
        ]);
    }
}
