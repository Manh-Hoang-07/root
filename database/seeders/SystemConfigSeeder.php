<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'key' => 'app.name',
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
                'key' => 'app.version',
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
                'key' => 'app.debug',
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
                'key' => 'app.timezone',
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
                'key' => 'mail.driver',
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
                'key' => 'mail.host',
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
                'key' => 'mail.port',
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
                'key' => 'mail.username',
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
                'key' => 'mail.password',
                'value' => '',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::EMAIL,
                'description' => 'SMTP Password',
                'is_public' => false,
                'is_encrypted' => true,
                'status' => 'active',
                'sort_order' => 5,
            ],

            // API Settings
            [
                'key' => 'api.rate_limit',
                'value' => '60',
                'type' => ConfigType::INTEGER,
                'group' => ConfigGroup::API,
                'description' => 'Giới hạn request per minute',
                'is_public' => true,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'key' => 'api.timeout',
                'value' => '30',
                'type' => ConfigType::INTEGER,
                'group' => ConfigGroup::API,
                'description' => 'Timeout cho API requests (seconds)',
                'is_public' => true,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'key' => 'api.cors_enabled',
                'value' => '1',
                'type' => ConfigType::BOOLEAN,
                'group' => ConfigGroup::API,
                'description' => 'Bật CORS cho API',
                'is_public' => true,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 3,
            ],

            // Cache Settings
            [
                'key' => 'cache.default',
                'value' => 'file',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::CACHE,
                'description' => 'Cache driver mặc định',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'key' => 'cache.ttl',
                'value' => '3600',
                'type' => ConfigType::INTEGER,
                'group' => ConfigGroup::CACHE,
                'description' => 'Cache TTL mặc định (seconds)',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 2,
            ],

            // Security Settings
            [
                'key' => 'security.password_min_length',
                'value' => '8',
                'type' => ConfigType::INTEGER,
                'group' => ConfigGroup::SECURITY,
                'description' => 'Độ dài tối thiểu của mật khẩu',
                'is_public' => true,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'key' => 'security.session_timeout',
                'value' => '120',
                'type' => ConfigType::INTEGER,
                'group' => ConfigGroup::SECURITY,
                'description' => 'Thời gian timeout session (minutes)',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'key' => 'security.max_login_attempts',
                'value' => '5',
                'type' => ConfigType::INTEGER,
                'group' => ConfigGroup::SECURITY,
                'description' => 'Số lần đăng nhập sai tối đa',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 3,
            ],

            // Storage Settings
            [
                'key' => 'storage.disk',
                'value' => 'local',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::STORAGE,
                'description' => 'Storage disk mặc định',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'key' => 'storage.max_file_size',
                'value' => '10240',
                'type' => ConfigType::INTEGER,
                'group' => ConfigGroup::STORAGE,
                'description' => 'Kích thước file tối đa (KB)',
                'is_public' => true,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'key' => 'storage.allowed_extensions',
                'value' => '["jpg","jpeg","png","gif","pdf","doc","docx","xls","xlsx"]',
                'type' => ConfigType::ARRAY,
                'group' => ConfigGroup::STORAGE,
                'description' => 'Các định dạng file được phép upload',
                'is_public' => true,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 3,
            ],

            // Notification Settings
            [
                'key' => 'notification.email_enabled',
                'value' => '1',
                'type' => ConfigType::BOOLEAN,
                'group' => ConfigGroup::NOTIFICATION,
                'description' => 'Bật thông báo email',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'key' => 'notification.sms_enabled',
                'value' => '0',
                'type' => ConfigType::BOOLEAN,
                'group' => ConfigGroup::NOTIFICATION,
                'description' => 'Bật thông báo SMS',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 2,
            ],

            // Custom Settings
            [
                'key' => 'custom.maintenance_mode',
                'value' => '0',
                'type' => ConfigType::BOOLEAN,
                'group' => ConfigGroup::CUSTOM,
                'description' => 'Chế độ bảo trì',
                'is_public' => true,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'key' => 'custom.maintenance_message',
                'value' => 'Hệ thống đang bảo trì, vui lòng quay lại sau!',
                'type' => ConfigType::STRING,
                'group' => ConfigGroup::CUSTOM,
                'description' => 'Thông báo bảo trì',
                'is_public' => true,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 2,
            ],
        ];

        foreach ($configs as $config) {
            SystemConfig::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}
