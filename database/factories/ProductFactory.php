<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Brand;
use App\Models\Category;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $productNames = [
            'iPhone', 'Samsung Galaxy', 'MacBook', 'Dell XPS', 'Sony WH', 'AirPods', 'iPad',
            'Surface', 'ThinkPad', 'ZenBook', 'ROG', 'Alienware', 'Razer Blade', 'MSI GS',
            'Gigabyte Aero', 'ASUS TUF', 'HP Pavilion', 'Lenovo IdeaPad', 'Acer Swift',
            'LG Gram', 'Huawei MateBook', 'Xiaomi Mi', 'OnePlus', 'Google Pixel'
        ];

        $productTypes = [
            'Pro', 'Ultra', 'Plus', 'Max', 'Mini', 'Air', 'Studio', 'Elite', 'Premium',
            'Standard', 'Basic', 'Lite', 'SE', 'X', 'Z', 'S', 'Note', 'Fold', 'Flip'
        ];

        $name = $this->faker->randomElement($productNames) . ' ' . $this->faker->randomElement($productTypes) . ' ' . $this->faker->numberBetween(10, 25);

        return [
            'name' => $name,
            'slug' => $this->faker->unique()->slug(),
            'code' => 'PRD-' . strtoupper($this->faker->bothify('????')) . '-' . $this->faker->numberBetween(1000, 9999),
            'description' => $this->faker->paragraph(4),
            'short_description' => $this->faker->sentence(10),
            'price' => $this->faker->numberBetween(1000000, 50000000),
            'sale_price' => null,
            'image' => 'products/' . strtolower(str_replace(' ', '-', $name)) . '.jpg',
            'weight' => $this->faker->randomFloat(2, 0.1, 5.0),
            'length' => $this->faker->numberBetween(10, 50),
            'width' => $this->faker->numberBetween(5, 30),
            'height' => $this->faker->numberBetween(1, 10),
            'brand_id' => Brand::inRandomOrder()->first()?->id ?? Brand::factory(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'attributes' => [
                'color' => $this->faker->randomElement(['Đen', 'Trắng', 'Xanh', 'Đỏ', 'Vàng', 'Xám']),
                'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL', 'XXL']),
                'material' => $this->faker->randomElement(['Nhựa', 'Kim loại', 'Gỗ', 'Vải', 'Da'])
            ],
        ];
    }
}
