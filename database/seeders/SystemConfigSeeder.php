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

            // Email Templates
            [
                'key' => 'email_template_welcome',
                'value' => '{"subject":"Chào mừng đến với {{app_name}}","content":"Xin chào {{name}},\\n\\nChào mừng bạn đến với {{app_name}}!\\n\\nCảm ơn bạn đã đăng ký tài khoản. Chúng tôi rất vui được chào đón bạn tham gia cộng đồng của chúng tôi.\\n\\nNếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi.\\n\\nTrân trọng,\\nĐội ngũ {{app_name}}","description":"Email chào mừng người dùng mới"}',
                'type' => ConfigType::JSON,
                'group' => 'email_templates',
                'description' => 'Email template chào mừng',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'key' => 'email_template_password_reset',
                'value' => '{"subject":"Đặt lại mật khẩu - {{app_name}}","content":"Xin chào {{name}},\\n\\nBạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình.\\n\\nMã xác thực của bạn là: {{reset_code}}\\n\\nMã này có hiệu lực trong 15 phút. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.\\n\\nTrân trọng,\\nĐội ngũ {{app_name}}","description":"Email đặt lại mật khẩu"}',
                'type' => ConfigType::JSON,
                'group' => 'email_templates',
                'description' => 'Email template đặt lại mật khẩu',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'key' => 'email_template_notification',
                'value' => '{"subject":"Thông báo từ {{app_name}}","content":"Xin chào {{name}},\\n\\n{{message}}\\n\\nThời gian: {{current_datetime}}\\n\\nTrân trọng,\\nĐội ngũ {{app_name}}","description":"Email thông báo chung"}',
                'type' => ConfigType::JSON,
                'group' => 'email_templates',
                'description' => 'Email template thông báo',
                'is_public' => false,
                'is_encrypted' => false,
                'status' => 'active',
                'sort_order' => 3,
            ],

            // Notification Settings
            [
                'key' => 'email_enabled',
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
                'key' => 'sms_enabled',
                'value' => '0',
                'type' => ConfigType::BOOLEAN,
                'group' => ConfigGroup::NOTIFICATION,
                'description' => 'Bật thông báo SMS',
                'is_public' => false,
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