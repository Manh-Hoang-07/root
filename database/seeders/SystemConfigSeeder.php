<?php

namespace Database\Seeders;

use App\Models\SystemConfig;
use Illuminate\Database\Seeder;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            // Cấu hình chung - Thông tin website
            [
                'config_key' => 'site_name',
                'config_value' => 'Website Name',
                'config_type' => 'string',
                'config_group' => 'general',
                'display_name' => 'Tên website',
                'description' => 'Tên hiển thị của website',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'config_key' => 'site_description',
                'config_value' => 'Mô tả website',
                'config_type' => 'string',
                'config_group' => 'general',
                'display_name' => 'Mô tả website',
                'description' => 'Mô tả ngắn về website',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'config_key' => 'site_logo',
                'config_value' => '/images/logo.png',
                'config_type' => 'file',
                'config_group' => 'general',
                'display_name' => 'Logo website',
                'description' => 'Logo chính của website',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'config_key' => 'site_favicon',
                'config_value' => '/images/favicon.ico',
                'config_type' => 'file',
                'config_group' => 'general',
                'display_name' => 'Favicon',
                'description' => 'Icon hiển thị trên tab trình duyệt',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 4
            ],
            [
                'config_key' => 'site_address',
                'config_value' => '123 Đường ABC, Quận XYZ, TP.HCM',
                'config_type' => 'string',
                'config_group' => 'general',
                'display_name' => 'Địa chỉ',
                'description' => 'Địa chỉ liên hệ',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 5
            ],
            [
                'config_key' => 'site_phone',
                'config_value' => '0123456789',
                'config_type' => 'string',
                'config_group' => 'general',
                'display_name' => 'Số điện thoại',
                'description' => 'Số điện thoại liên hệ',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 6
            ],
            [
                'config_key' => 'site_email',
                'config_value' => 'contact@website.com',
                'config_type' => 'email',
                'config_group' => 'general',
                'display_name' => 'Email liên hệ',
                'description' => 'Email liên hệ chính',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 7
            ],
            [
                'config_key' => 'timezone',
                'config_value' => 'Asia/Ho_Chi_Minh',
                'config_type' => 'string',
                'config_group' => 'general',
                'display_name' => 'Múi giờ',
                'description' => 'Múi giờ của hệ thống',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 8
            ],
            [
                'config_key' => 'currency',
                'config_value' => 'VND',
                'config_type' => 'string',
                'config_group' => 'general',
                'display_name' => 'Đơn vị tiền tệ',
                'description' => 'Đơn vị tiền tệ mặc định',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 9
            ],
            [
                'config_key' => 'maintenance_mode',
                'config_value' => 'false',
                'config_type' => 'boolean',
                'config_group' => 'general',
                'display_name' => 'Chế độ bảo trì',
                'description' => 'Bật/tắt chế độ bảo trì website',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 10
            ],

            // Cấu hình email
            [
                'config_key' => 'mail_mailer',
                'config_value' => 'log',
                'config_type' => 'string',
                'config_group' => 'email',
                'display_name' => 'Mail Driver',
                'description' => 'Phương thức gửi email (smtp, log, mailgun)',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'config_key' => 'mail_scheme',
                'config_value' => 'null',
                'config_type' => 'string',
                'config_group' => 'email',
                'display_name' => 'Mail Scheme',
                'description' => 'Giao thức kết nối (tls, ssl, null)',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'config_key' => 'mail_host',
                'config_value' => '127.0.0.1',
                'config_type' => 'string',
                'config_group' => 'email',
                'display_name' => 'Mail Host',
                'description' => 'Địa chỉ server email',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 3
            ],
            [
                'config_key' => 'mail_port',
                'config_value' => '2525',
                'config_type' => 'number',
                'config_group' => 'email',
                'display_name' => 'Mail Port',
                'description' => 'Cổng kết nối email',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 4
            ],
            [
                'config_key' => 'mail_username',
                'config_value' => 'null',
                'config_type' => 'string',
                'config_group' => 'email',
                'display_name' => 'Mail Username',
                'description' => 'Tên đăng nhập email',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 5
            ],
            [
                'config_key' => 'mail_password',
                'config_value' => 'null',
                'config_type' => 'string',
                'config_group' => 'email',
                'display_name' => 'Mail Password',
                'description' => 'Mật khẩu email',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 6
            ],
            [
                'config_key' => 'mail_from_address',
                'config_value' => 'hello@example.com',
                'config_type' => 'email',
                'config_group' => 'email',
                'display_name' => 'Mail From Address',
                'description' => 'Địa chỉ email gửi đi',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 7
            ],
            [
                'config_key' => 'mail_from_name',
                'config_value' => '${APP_NAME}',
                'config_type' => 'string',
                'config_group' => 'email',
                'display_name' => 'Mail From Name',
                'description' => 'Tên hiển thị khi gửi email',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 8
            ],

            // Cấu hình bảo mật
            [
                'config_key' => 'session_lifetime',
                'config_value' => '120',
                'config_type' => 'number',
                'config_group' => 'security',
                'display_name' => 'Thời gian session (phút)',
                'description' => 'Thời gian hết hạn session',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'config_key' => 'max_login_attempts',
                'config_value' => '5',
                'config_type' => 'number',
                'config_group' => 'security',
                'display_name' => 'Số lần đăng nhập sai tối đa',
                'description' => 'Số lần đăng nhập sai trước khi khóa tài khoản',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'config_key' => 'lockout_duration',
                'config_value' => '15',
                'config_type' => 'number',
                'config_group' => 'security',
                'display_name' => 'Thời gian khóa tài khoản (phút)',
                'description' => 'Thời gian khóa tài khoản sau khi đăng nhập sai',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 3
            ],
            [
                'config_key' => 'password_min_length',
                'config_value' => '8',
                'config_type' => 'number',
                'config_group' => 'security',
                'display_name' => 'Độ dài mật khẩu tối thiểu',
                'description' => 'Số ký tự tối thiểu cho mật khẩu',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 4
            ],
            [
                'config_key' => 'require_special_chars',
                'config_value' => 'true',
                'config_type' => 'boolean',
                'config_group' => 'security',
                'display_name' => 'Yêu cầu ký tự đặc biệt',
                'description' => 'Mật khẩu phải có ký tự đặc biệt',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 5
            ],
            [
                'config_key' => 'require_uppercase',
                'config_value' => 'true',
                'config_type' => 'boolean',
                'config_group' => 'security',
                'display_name' => 'Yêu cầu chữ hoa',
                'description' => 'Mật khẩu phải có chữ hoa',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 6
            ],
            [
                'config_key' => 'require_numbers',
                'config_value' => 'true',
                'config_type' => 'boolean',
                'config_group' => 'security',
                'display_name' => 'Yêu cầu số',
                'description' => 'Mật khẩu phải có số',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 7
            ],
            [
                'config_key' => 'two_factor_enabled',
                'config_value' => 'false',
                'config_type' => 'boolean',
                'config_group' => 'security',
                'display_name' => 'Bật xác thực 2 yếu tố',
                'description' => 'Bật/tắt xác thực 2 yếu tố',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 8
            ],
            [
                'config_key' => 'rate_limit_requests',
                'config_value' => '100',
                'config_type' => 'number',
                'config_group' => 'security',
                'display_name' => 'Giới hạn request (phút)',
                'description' => 'Số request tối đa mỗi phút',
                'is_public' => false,
                'is_required' => true,
                'sort_order' => 9
            ]
        ];

        foreach ($configs as $config) {
            SystemConfig::updateOrCreate(
                ['config_key' => $config['config_key']],
                array_merge($config, ['status' => 'active'])
            );
        }
    }
}
