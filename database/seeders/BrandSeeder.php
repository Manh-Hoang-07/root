<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'description' => 'Apple Inc. là công ty công nghệ đa quốc gia của Mỹ chuyên về thiết kế và phát triển các sản phẩm điện tử tiêu dùng, phần mềm máy tính và dịch vụ trực tuyến.',
                'image' => 'apple-logo.png',
                'status' => 'active',
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'description' => 'Samsung Group là tập đoàn đa quốc gia của Hàn Quốc, chuyên về điện tử tiêu dùng, bán dẫn, thiết bị di động và nhiều lĩnh vực khác.',
                'image' => 'samsung-logo.png',
                'status' => 'active',
            ],
            [
                'name' => 'Dell',
                'slug' => 'dell',
                'description' => 'Dell Technologies Inc. là công ty công nghệ đa quốc gia của Mỹ chuyên về phát triển, bán, sửa chữa và hỗ trợ máy tính cá nhân.',
                'image' => 'dell-logo.png',
                'status' => 'active',
            ],
            [
                'name' => 'Sony',
                'slug' => 'sony',
                'description' => 'Sony Group Corporation là tập đoàn đa quốc gia của Nhật Bản chuyên về điện tử tiêu dùng, giải trí và công nghệ.',
                'image' => 'sony-logo.png',
                'status' => 'active',
            ],
            [
                'name' => 'Microsoft',
                'slug' => 'microsoft',
                'description' => 'Microsoft Corporation là công ty công nghệ đa quốc gia của Mỹ chuyên về phát triển phần mềm, thiết bị điện tử và dịch vụ đám mây.',
                'image' => 'microsoft-logo.png',
                'status' => 'active',
            ],
            [
                'name' => 'HP',
                'slug' => 'hp',
                'description' => 'HP Inc. là công ty công nghệ đa quốc gia của Mỹ chuyên về phát triển và sản xuất máy tính cá nhân, máy in và các thiết bị liên quan.',
                'image' => 'hp-logo.png',
                'status' => 'active',
            ],
            [
                'name' => 'Lenovo',
                'slug' => 'lenovo',
                'description' => 'Lenovo Group Limited là công ty công nghệ đa quốc gia của Trung Quốc chuyên về phát triển, thiết kế, sản xuất và bán máy tính cá nhân.',
                'image' => 'lenovo-logo.png',
                'status' => 'active',
            ],
            [
                'name' => 'Asus',
                'slug' => 'asus',
                'description' => 'ASUSTeK Computer Inc. là công ty đa quốc gia của Đài Loan chuyên về phát triển và sản xuất máy tính, điện thoại di động và các thiết bị điện tử.',
                'image' => 'asus-logo.png',
                'status' => 'active',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
