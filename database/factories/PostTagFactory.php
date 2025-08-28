<?php

namespace Database\Factories;

use App\Enums\BasicStatus;
use App\Models\PostTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostTagFactory extends Factory
{
    protected $model = PostTag::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(rand(1, 2), true);
        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->optional()->sentence(8),
            'status' => BasicStatus::Active,
            'meta_title' => $this->faker->optional()->sentence(6),
            'meta_description' => $this->faker->optional()->sentence(12),
            'canonical_url' => null,
        ];
    }
}
