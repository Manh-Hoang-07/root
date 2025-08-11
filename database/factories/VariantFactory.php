<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class VariantFactory extends Factory
{
    public function definition(): array
    {
        $capacities = ['64GB', '128GB', '256GB', '512GB', '1TB', '2TB'];
        $colors = ['Đen', 'Trắng', 'Xanh', 'Đỏ', 'Vàng', 'Xám', 'Titan', 'Bạc', 'Vàng Rose'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        
        $product = Product::inRandomOrder()->first() ?? Product::factory();
        $capacity = $this->faker->randomElement($capacities);
        $color = $this->faker->randomElement($colors);
        $size = $this->faker->randomElement($sizes);
        
        $name = $product->name . ' ' . $capacity . ' ' . $color;
        $sku = 'VAR-' . strtoupper($this->faker->bothify('????')) . '-' . $this->faker->numberBetween(100, 999);

        return [
            'product_id' => $product->id,
            'name' => $name,
            'sku' => $sku,
            'price' => $this->faker->numberBetween(1000000, 50000000),
            'sale_price' => $this->faker->optional(0.3)->numberBetween(500000, 25000000),
            'quantity' => $this->faker->numberBetween(0, 1000),
            'image' => 'variants/' . strtolower(str_replace(' ', '-', $name)) . '.jpg',
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
