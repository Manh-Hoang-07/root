<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Kho Hà Nội',
                'address' => '123 Đường Láng, Đống Đa, Hà Nội',
                'city' => 'Hà Nội',
                'province' => 'Hà Nội',
                'phone' => '024-1234-5678',
                'email' => 'hanoi@warehouse.com',
                'status' => 'active',
            ],
            [
                'name' => 'Kho TP.HCM',
                'address' => '456 Đường Nguyễn Huệ, Quận 1, TP.HCM',
                'city' => 'TP.HCM',
                'province' => 'TP.HCM',
                'phone' => '028-8765-4321',
                'email' => 'hcm@warehouse.com',
                'status' => 'active',
            ],
            [
                'name' => 'Kho Đà Nẵng',
                'address' => '789 Đường Trần Phú, Hải Châu, Đà Nẵng',
                'city' => 'Đà Nẵng',
                'province' => 'Đà Nẵng',
                'phone' => '0236-1111-2222',
                'email' => 'danang@warehouse.com',
                'status' => 'active',
            ],
            [
                'name' => 'Kho Cần Thơ',
                'address' => '321 Đường 30/4, Ninh Kiều, Cần Thơ',
                'city' => 'Cần Thơ',
                'province' => 'Cần Thơ',
                'phone' => '0292-3333-4444',
                'email' => 'cantho@warehouse.com',
                'status' => 'active',
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
}
