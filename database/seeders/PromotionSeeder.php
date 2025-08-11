<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Promotion;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promotions = [
            [
                'name' => 'Khuyến mãi mùa hè 2024',
                'type' => 'product',
                'discount_type' => 'percent',
                'discount_value' => 15,
                'min_order_value' => 1000000,
                'start_at' => '2024-06-01 00:00:00',
                'end_at' => '2024-08-31 23:59:59',
                'status' => 'active',
            ],
            [
                'name' => 'Giảm giá iPhone',
                'type' => 'product',
                'discount_type' => 'fixed',
                'discount_value' => 2000000,
                'min_order_value' => 20000000,
                'start_at' => '2024-07-01 00:00:00',
                'end_at' => '2024-12-31 23:59:59',
                'status' => 'active',
            ],
            [
                'name' => 'Khuyến mãi laptop gaming',
                'type' => 'product',
                'discount_type' => 'percent',
                'discount_value' => 20,
                'min_order_value' => 30000000,
                'start_at' => '2024-07-15 00:00:00',
                'end_at' => '2024-09-15 23:59:59',
                'status' => 'active',
            ],
            [
                'name' => 'Giảm giá phụ kiện',
                'type' => 'category',
                'discount_type' => 'percent',
                'discount_value' => 25,
                'min_order_value' => 500000,
                'start_at' => '2024-08-01 00:00:00',
                'end_at' => '2024-10-31 23:59:59',
                'status' => 'active',
            ],
            [
                'name' => 'Khuyến mãi Black Friday',
                'type' => 'order',
                'discount_type' => 'percent',
                'discount_value' => 50,
                'min_order_value' => 2000000,
                'start_at' => '2024-11-29 00:00:00',
                'end_at' => '2024-12-02 23:59:59',
                'status' => 'inactive',
            ],
            [
                'name' => 'Giảm giá sinh viên',
                'type' => 'order',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_order_value' => 1000000,
                'start_at' => '2024-01-01 00:00:00',
                'end_at' => '2024-12-31 23:59:59',
                'status' => 'active',
            ],
            [
                'name' => 'Miễn phí vận chuyển',
                'type' => 'ship',
                'discount_type' => 'free',
                'discount_value' => 0,
                'min_order_value' => 500000,
                'start_at' => '2024-01-01 00:00:00',
                'end_at' => '2024-12-31 23:59:59',
                'status' => 'active',
            ],
            [
                'name' => 'Giảm giá cuối năm',
                'type' => 'order',
                'discount_type' => 'percent',
                'discount_value' => 30,
                'min_order_value' => 5000000,
                'start_at' => '2024-12-20 00:00:00',
                'end_at' => '2024-12-31 23:59:59',
                'status' => 'active',
            ],
        ];

        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
    }
}
