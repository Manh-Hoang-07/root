<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['pending', 'confirmed', 'shipping', 'delivered', 'cancelled', 'completed'];
        $paymentMethods = ['cod', 'bank_transfer', 'credit_card', 'paypal', 'momo', 'vnpay'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'status' => $this->faker->randomElement($statuses),
            'total_price' => $this->faker->numberBetween(100000, 50000000),
            'shipping_fee' => $this->faker->numberBetween(0, 500000),
            'shipping_discount' => $this->faker->numberBetween(0, 200000),
            'promotion_discount' => $this->faker->numberBetween(0, 2000000),
            'final_price' => 0, // Sẽ được tính tự động
            'shipping_address_id' => \App\Models\Address::inRandomOrder()->first()?->id ?? \App\Models\Address::factory(),
            'note' => $this->faker->optional(0.3)->sentence(10),
            'cancelled_at' => $this->faker->optional(0.1)->dateTimeBetween('-1 year', 'now'),
            'completed_at' => $this->faker->optional(0.5)->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
