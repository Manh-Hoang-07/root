<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addresses = [
            [
                'user_id' => 1, // Admin user
                'name' => 'Nguyễn Văn A',
                'phone' => '0901234567',
                'address_line' => '123 Đường ABC, Phường XYZ',
                'city' => 'Hà Nội',
                'province' => 'Hà Nội',
                'district' => 'Ba Đình',
                'ward' => 'Phường ABC',
                'postal_code' => '100000',
                'is_default' => true,
            ],
            [
                'user_id' => 1, // Admin user
                'name' => 'Nguyễn Văn A',
                'phone' => '0901234567',
                'address_line' => '456 Đường DEF, Tầng 5, Tòa nhà GHI',
                'city' => 'Hà Nội',
                'province' => 'Hà Nội',
                'district' => 'Cầu Giấy',
                'ward' => 'Phường DEF',
                'postal_code' => '100000',
                'is_default' => false,
            ],
            [
                'user_id' => 2, // Regular user
                'name' => 'Trần Thị B',
                'phone' => '0909876543',
                'address_line' => '789 Đường JKL, Phường MNO',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'district' => 'Quận 1',
                'ward' => 'Phường JKL',
                'postal_code' => '700000',
                'is_default' => true,
            ],
            [
                'user_id' => 3, // Another user
                'name' => 'Lê Văn C',
                'phone' => '0905555555',
                'address_line' => '321 Đường PQR, Phường STU',
                'city' => 'Đà Nẵng',
                'province' => 'Đà Nẵng',
                'district' => 'Hải Châu',
                'ward' => 'Phường PQR',
                'postal_code' => '500000',
                'is_default' => true,
            ],
            [
                'user_id' => 4, // Another user
                'name' => 'Phạm Thị D',
                'phone' => '0907777777',
                'address_line' => '654 Đường VWX, Phường YZ',
                'city' => 'Hải Phòng',
                'province' => 'Hải Phòng',
                'district' => 'Hồng Bàng',
                'ward' => 'Phường VWX',
                'postal_code' => '300000',
                'is_default' => true,
            ],
        ];

        foreach ($addresses as $address) {
            Address::create($address);
        }
    }
}
