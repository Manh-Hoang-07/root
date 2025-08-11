<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    public function definition(): array
    {
        $brandName = $this->faker->unique()->company();
        return [
            'name' => $brandName,
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(3),
            'image' => 'brands/' . strtolower(str_replace(' ', '-', $brandName)) . '.jpg',
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
