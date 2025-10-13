<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemConfig;
use App\Enums\ConfigType;
use App\Enums\ConfigGroup;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            // General Settings
            [
                'key' => 'name',
                'value' => 'Laravel System',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::GENERAL,
                'description' => 'Tên ứng dụng',
                'is_public' => true,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'key' => 'version',
                'value' => '1.0.0',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::GENERAL,
                'description' => 'Phiên bản ứng dụng',
                'is_public' => true,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'key' => 'debug',
                'value' => '0',
                'type' => ConfigType::BOOLEAN,
                'group' => ConfigGroup::GENERAL,
                'description' => 'Chế độ debug',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Ho_Chi_Minh',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::GENERAL,
                'description' => 'Múi giờ hệ thống',
                'is_public' => true,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 4,
            ],

            // Email Settings
            [
                'key' => 'driver',
                'value' => 'smtp',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::EMAIL,
                'description' => 'Driver gửi email',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'key' => 'host',
                'value' => 'smtp.gmail.com',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::EMAIL,
                'description' => 'SMTP Host',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'key' => 'port',
                'value' => '587',
                'type' => ConfigType::INTEGER,
                'group' => ConfigGroup::EMAIL,
                'description' => 'SMTP Port',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'key' => 'username',
                'value' => '',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::EMAIL,
                'description' => 'SMTP Username',
                'is_public' => false,
                'is_encrypted' => true,
                'status' => 'active',
                'sort_order' => 4,
            ],
            [
                'key' => 'password',
                'value' => '',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::EMAIL,
                'description' => 'SMTP Password',
                'is_public' => false,
                'is_encrypted' => true,
                'status' => 'active',
                'sort_order' => 5,
            ],
            [
                'key' => 'from_address',
                'value' => 'noreply@example.com',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::EMAIL,
                'description' => 'Email địa chỉ gửi',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 6,
            ],
            [
                'key' => 'from_name',
                'value' => 'Laravel System',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::EMAIL,
                'description' => 'Tên người gửi email',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 7,
            ],

        ];

        foreach ($configs as $config) {
            SystemConfig::updateOrCreate(
                ['key' => $config['key']],
                array_merge($config, [
                    'created_user_id' => 1,
                    'updated_user_id' => 1,
                ])
            );
        }
    }
}