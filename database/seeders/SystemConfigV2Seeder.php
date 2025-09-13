<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemConfigV2;

class SystemConfigV2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            // General configs - Thông tin cơ bản website
            [
                'key' => 'app_name',
                'value' => 'Laravel App',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Tên ứng dụng',
                'type' => 'string'
            ],
            [
                'key' => 'site_name',
                'value' => 'Website Demo',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Tên website',
                'type' => 'string'
            ],
            [
                'key' => 'site_logo',
                'value' => '/images/logo.png',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Logo website',
                'type' => 'string'
            ],
            [
                'key' => 'site_favicon',
                'value' => '/images/favicon.ico',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Favicon website',
                'type' => 'string'
            ],
            [
                'key' => 'site_email',
                'value' => 'admin@demo.com',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Email liên hệ',
                'type' => 'string'
            ],
            [
                'key' => 'site_phone',
                'value' => '0123456789',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Số điện thoại liên hệ',
                'type' => 'string'
            ],
            [
                'key' => 'site_address',
                'value' => '123 Đường ABC, Quận XYZ, TP.HCM',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Địa chỉ website',
                'type' => 'text'
            ],
            [
                'key' => 'site_description',
                'value' => 'Website bán hàng trực tuyến với nhiều sản phẩm chất lượng',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Mô tả website',
                'type' => 'text'
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Ho_Chi_Minh',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Múi giờ',
                'type' => 'string'
            ],
            [
                'key' => 'language',
                'value' => 'vi',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Ngôn ngữ mặc định',
                'type' => 'string'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'group' => 'general',
                'is_public' => false,
                'description' => 'Chế độ bảo trì',
                'type' => 'boolean'
            ],
            [
                'key' => 'items_per_page',
                'value' => '20',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Số item mỗi trang',
                'type' => 'number'
            ],

            // Email configs
            [
                'key' => 'smtp_host',
                'value' => 'smtp.gmail.com',
                'group' => 'email',
                'is_public' => false,
                'description' => 'SMTP Host',
                'type' => 'string'
            ],
            [
                'key' => 'smtp_port',
                'value' => '587',
                'group' => 'email',
                'is_public' => false,
                'description' => 'SMTP Port',
                'type' => 'number'
            ],
            [
                'key' => 'smtp_username',
                'value' => 'your-email@gmail.com',
                'group' => 'email',
                'is_public' => false,
                'description' => 'SMTP Username',
                'type' => 'string'
            ],
            [
                'key' => 'smtp_password',
                'value' => 'your-app-password',
                'group' => 'email',
                'is_public' => false,
                'description' => 'SMTP Password',
                'type' => 'string'
            ],
            [
                'key' => 'smtp_encryption',
                'value' => 'tls',
                'group' => 'email',
                'is_public' => false,
                'description' => 'SMTP Encryption',
                'type' => 'string'
            ],
            [
                'key' => 'from_email',
                'value' => 'noreply@example.com',
                'group' => 'email',
                'is_public' => false,
                'description' => 'Email gửi đi',
                'type' => 'string'
            ],
            [
                'key' => 'from_name',
                'value' => 'Laravel App',
                'group' => 'email',
                'is_public' => false,
                'description' => 'Tên người gửi',
                'type' => 'string'
            ]
        ];

        foreach ($configs as $config) {
            SystemConfigV2::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}
