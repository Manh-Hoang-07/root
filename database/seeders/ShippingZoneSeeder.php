<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShippingZone;

class ShippingZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Hà Nội',
                'provinces' => ['Hà Nội'],
                'districts' => ['Cầu Giấy', 'Đống Đa', 'Hai Bà Trưng', 'Hoàn Kiếm', 'Tây Hồ'],
                'base_fee' => 20000,
                'weight_fee' => 5000,
                'estimated_days' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'TP.HCM',
                'provinces' => ['TP.HCM'],
                'districts' => ['Quận 1', 'Quận 3', 'Quận 5', 'Quận 7', 'Quận 10'],
                'base_fee' => 25000,
                'weight_fee' => 6000,
                'estimated_days' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Đà Nẵng',
                'provinces' => ['Đà Nẵng'],
                'districts' => ['Hải Châu', 'Thanh Khê', 'Sơn Trà', 'Ngũ Hành Sơn'],
                'base_fee' => 30000,
                'weight_fee' => 7000,
                'estimated_days' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Cần Thơ',
                'provinces' => ['Cần Thơ'],
                'districts' => ['Ninh Kiều', 'Bình Thủy', 'Cái Răng', 'Ô Môn'],
                'base_fee' => 35000,
                'weight_fee' => 8000,
                'estimated_days' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'Miền Bắc',
                'provinces' => ['Hải Phòng', 'Quảng Ninh', 'Thái Nguyên', 'Bắc Ninh'],
                'districts' => [],
                'base_fee' => 40000,
                'weight_fee' => 10000,
                'estimated_days' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'Miền Nam',
                'provinces' => ['Bình Dương', 'Đồng Nai', 'Vũng Tàu', 'Long An'],
                'districts' => [],
                'base_fee' => 45000,
                'weight_fee' => 12000,
                'estimated_days' => 4,
                'status' => 'active',
            ],
        ];

        foreach ($zones as $zone) {
            ShippingZone::create($zone);
        }
    }
}
