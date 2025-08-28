<?php

namespace Database\Factories;

use App\Enums\BasicStatus;
use App\Models\PostCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostCategoryFactory extends Factory
{
    protected $model = PostCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(rand(1, 3), true);
        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->optional()->sentence(10),
            'parent_id' => null,
            'image' => $this->faker->optional()->imageUrl(640, 480, 'business', true),
            'status' => BasicStatus::Active,
            'meta_title' => $this->faker->optional()->sentence(6),
            'meta_description' => $this->faker->optional()->sentence(12),
            'canonical_url' => null,
            'og_image' => null,
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
