<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostTag;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $categories = PostCategory::inRandomOrder()->take(8)->get();
        if ($categories->isEmpty()) {
            $this->call(PostCategorySeeder::class);
            $categories = PostCategory::inRandomOrder()->take(8)->get();
        }

        $tags = PostTag::inRandomOrder()->take(10)->get();
        if ($tags->isEmpty()) {
            $this->call(PostTagSeeder::class);
            $tags = PostTag::inRandomOrder()->take(10)->get();
        }

        // Create posts
        Post::factory()->count(40)->create()->each(function (Post $post) use ($categories, $tags) {
            // Assign primary category
            $primary = $categories->random();
            $post->primary_postcategory_id = $primary->id;
            $post->save();

            // Attach additional categories
            $extraCategories = $categories->random(rand(0, 3))->pluck('id')->all();
            $post->categories()->syncWithoutDetaching($extraCategories);

            // Attach tags
            $tagIds = $tags->random(rand(1, 4))->pluck('id')->all();
            $post->tags()->sync($tagIds);
        });
    }
}


