<?php

namespace App\Services\Core\Email;

use App\Models\NotificationTemplate;
use App\Mail\SimpleEmail;
use App\Services\Core\SystemConfig\SystemConfigService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailService
{
    protected SystemConfigService $configService;

    public function __construct(SystemConfigService $configService)
    {
        $this->configService = $configService;
    }
    /**
     * Gửi email đơn giản với tiêu đề và nội dung
     */
    /**
     * Lấy cấu hình email từ database và cập nhật vào config
     */
    public function getConfig(): bool
    {
        try {
            // Lấy tất cả cấu hình email từ database
            $emailConfigs = $this->configService->getByGroup('email');
            
            if (empty($emailConfigs)) {
                Log::warning('Không tìm thấy cấu hình email trong database');
                return false;
            }

            // Chuyển đổi array thành key-value pairs
            $configs = [];
            foreach ($emailConfigs as $config) {
                $configs[$config['key']] = $config['value'];
            }

            // Cập nhật cấu hình mail trong Laravel
            $this->updateConfig($configs);

            Log::info('Đã tải cấu hình email từ database thành công');
            return true;

        } catch (Exception $e) {
            Log::error('Lỗi khi tải cấu hình email từ database: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật cấu hình mail trong Laravel
     */
    protected function updateConfig(array $configs): void
    {
        // Cập nhật driver
        if (isset($configs['driver'])) {
            Config::set('mail.default', $configs['driver']);
            Config::set('mail.mailers.smtp.transport', $configs['driver']);
        }

        // Cập nhật SMTP settings
        if (isset($configs['host'])) {
            Config::set('mail.mailers.smtp.host', $configs['host']);
        }

        if (isset($configs['port'])) {
            Config::set('mail.mailers.smtp.port', (int) $configs['port']);
        }

        if (isset($configs['username'])) {
            Config::set('mail.mailers.smtp.username', $configs['username']);
        }

        if (isset($configs['password'])) {
            Config::set('mail.mailers.smtp.password', $configs['password']);
        }

        // Cập nhật encryption (mặc định là tls cho port 587)
        $port = (int) ($configs['port'] ?? 587);
        if ($port === 587) {
            Config::set('mail.mailers.smtp.encryption', 'tls');
        } elseif ($port === 465) {
            Config::set('mail.mailers.smtp.encryption', 'ssl');
        } else {
            Config::set('mail.mailers.smtp.encryption', null);
        }

        // Cập nhật timeout và các settings khác
        Config::set('mail.mailers.smtp.timeout', 60);
        Config::set('mail.mailers.smtp.verify_peer', false);

        // Cập nhật from address nếu có
        if (isset($configs['from_address'])) {
            Config::set('mail.from.address', $configs['from_address']);
        }
        if (isset($configs['from_name'])) {
            Config::set('mail.from.name', $configs['from_name']);
        }
    }


    public function send(string $to, string $subject, string $content, array $data = []): array
    {
        try {
            // Đảm bảo cấu hình email đã được load
            $this->getConfig();

            // Gửi email
            Mail::to($to)->send(new SimpleEmail($subject, $content, $data));

            Log::info("Email sent successfully to: {$to}", [
                'subject' => $subject,
                'to' => $to
            ]);

            return [
                'success' => true,
                'message' => 'Email đã được gửi thành công',
                'to' => $to,
                'subject' => $subject
            ];

        } catch (Exception $e) {
            Log::error("Failed to send email to: {$to}", [
                'error' => $e->getMessage(),
                'subject' => $subject,
                'to' => $to
            ]);

            return [
                'success' => false,
                'message' => 'Lỗi khi gửi email: ' . $e->getMessage(),
                'to' => $to,
                'subject' => $subject
            ];
        }
    }

    /**
     * Gửi email với template từ database
     */
    public function sendWithTemplate(string $to, string $templateCode, array $data = [], string $locale = 'vi'): array
    {
        try {
            // Lấy template từ database
            $template = NotificationTemplate::getByCode($templateCode, $locale);
            
            if (!$template) {
                return [
                    'success' => false,
                    'message' => "Không tìm thấy template: {$templateCode}"
                ];
            }

            // Thay thế placeholders
            $processed = $template->replacePlaceholders($data);

            return $this->send($to, $processed['subject'], $processed['content'], $data);

        } catch (Exception $e) {
            Log::error("Failed to send email with template: {$templateCode}", [
                'error' => $e->getMessage(),
                'to' => $to,
                'template' => $templateCode
            ]);

            return [
                'success' => false,
                'message' => 'Lỗi khi gửi email với template: ' . $e->getMessage()
            ];
        }
    }

}
