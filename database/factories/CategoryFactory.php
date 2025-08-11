<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $categoryName = $this->faker->unique()->words(2, true);
        return [
            'name' => ucwords($categoryName),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(2),
            'image' => 'categories/' . strtolower(str_replace(' ', '-', $categoryName)) . '.jpg',
            'parent_id' => null,
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
