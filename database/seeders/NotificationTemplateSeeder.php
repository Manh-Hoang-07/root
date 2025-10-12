<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'type' => 'email',
                'code' => 'WELCOME_EMAIL',
                'name' => 'Email chào mừng',
                'subject' => 'Chào mừng đến với {{app_name}}',
                'content' => "Xin chào {{name}},\n\nChào mừng bạn đến với {{app_name}}!\n\nCảm ơn bạn đã đăng ký tài khoản. Chúng tôi rất vui được chào đón bạn tham gia cộng đồng của chúng tôi.\n\nNếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi.\n\nTrân trọng,\nĐội ngũ {{app_name}}",
                'variables' => ['name'],
                'locale' => 'vi',
                'status' => 1,
            ],
            [
                'type' => 'email',
                'code' => 'OTP_VERIFY',
                'name' => 'Email xác thực OTP',
                'subject' => 'Mã xác thực OTP - {{app_name}}',
                'content' => "Xin chào {{name}},\n\nBạn đã yêu cầu mã xác thực OTP.\n\nMã xác thực của bạn là: {{otp}}\n\nMã này có hiệu lực trong 5 phút. Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.\n\nTrân trọng,\nĐội ngũ {{app_name}}",
                'variables' => ['name', 'otp'],
                'locale' => 'vi',
                'status' => 1,
            ],
            [
                'type' => 'email',
                'code' => 'PASSWORD_RESET',
                'name' => 'Email đặt lại mật khẩu',
                'subject' => 'Đặt lại mật khẩu - {{app_name}}',
                'content' => "Xin chào {{name}},\n\nBạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình.\n\nMã xác thực của bạn là: {{reset_code}}\n\nMã này có hiệu lực trong 15 phút. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.\n\nTrân trọng,\nĐội ngũ {{app_name}}",
                'variables' => ['name', 'reset_code'],
                'locale' => 'vi',
                'status' => 1,
            ],
            [
                'type' => 'email',
                'code' => 'NOTIFICATION_EMAIL',
                'name' => 'Email thông báo chung',
                'subject' => 'Thông báo từ {{app_name}}',
                'content' => "Xin chào {{name}},\n\n{{message}}\n\nThời gian: {{current_datetime}}\n\nTrân trọng,\nĐội ngũ {{app_name}}",
                'variables' => ['name', 'message'],
                'locale' => 'vi',
                'status' => 1,
            ],
            [
                'type' => 'sms',
                'code' => 'OTP_SMS',
                'name' => 'SMS xác thực OTP',
                'subject' => null,
                'content' => "Mã xác thực của bạn là: {{otp}}. Mã có hiệu lực trong 5 phút. {{app_name}}",
                'variables' => ['otp'],
                'locale' => 'vi',
                'status' => 1,
            ],
            [
                'type' => 'otp',
                'code' => 'OTP_VERIFY_CODE',
                'name' => 'OTP xác thực',
                'subject' => null,
                'content' => "{{otp}}",
                'variables' => ['otp'],
                'locale' => 'vi',
                'status' => 1,
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['code' => $template['code'], 'locale' => $template['locale']],
                $template
            );
        }
    }
}
