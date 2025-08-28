<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(4);
        $status = $this->faker->randomElement(PostStatus::values());
        $publishedAt = $status === PostStatus::PUBLISHED->value ? $this->faker->dateTimeBetween('-30 days', 'now') : null;

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'excerpt' => $this->faker->optional()->paragraph(2),
            'content' => $this->faker->paragraphs(rand(3, 7), true),
            'image' => $this->faker->optional()->imageUrl(800, 600, 'business', true),
            'cover_image' => $this->faker->optional()->imageUrl(1200, 630, 'business', true),
            'primary_postcategory_id' => null,
            'status' => $status,
            'is_featured' => $this->faker->boolean(10),
            'is_pinned' => $this->faker->boolean(5),
            'published_at' => $publishedAt,
            'view_count' => $this->faker->numberBetween(0, 10000),
            'meta_title' => $this->faker->optional()->sentence(6),
            'meta_description' => $this->faker->optional()->sentence(12),
            'canonical_url' => null,
            'og_title' => $this->faker->optional()->sentence(6),
            'og_description' => $this->faker->optional()->sentence(12),
            'og_image' => null,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}
