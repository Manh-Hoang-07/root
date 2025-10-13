<?php

namespace App\Services\Core\Email;

use App\Models\NotificationTemplate;
use App\Services\Core\SystemConfig\SystemConfigService;
use App\Helpers\SystemConfigHelper;
use App\Libraries\Core\CacheService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
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
     * Lấy cấu hình email từ database và cập nhật vào config (có cache)
     */
    public function getConfig(): bool
    {
        try {
            // Lấy từ cache trước
            $cached = CacheService::get('config', 'email');
            if ($cached !== null) {
                $this->updateConfig($cached);
                return true;
            }

            // Nếu không có cache thì lấy từ database
            $emailConfigs = $this->configService->getByGroup('email');
            
            if (empty($emailConfigs)) {
                return false;
            }

            // Chuyển đổi array thành key-value pairs
            $configs = [];
            foreach ($emailConfigs as $config) {
                $configs[$config['key']] = $config['value'];
            }

            // Cache lại
            CacheService::put('config', $configs, 3600, 'email');

            // Cập nhật cấu hình mail trong Laravel
            $this->updateConfig($configs);

            return true;

        } catch (Exception $e) {
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


    /**
     * Gửi email (text hoặc HTML)
     */
    public function send(string $to, string $subject, string $content, array $data = []): array
    {
        try {
            // Đảm bảo cấu hình email đã được load
            $this->getConfig();

            // Nếu có data thì tạo HTML, không thì gửi text thuần
            if (!empty($data)) {
                $htmlContent = $this->formatHtmlContent($subject, $content, $data);
                Mail::html($htmlContent, function ($message) use ($to, $subject) {
                    $message->to($to)->subject($subject);
                });
            } else {
                Mail::raw($content, function ($message) use ($to, $subject) {
                    $message->to($to)->subject($subject);
                });
            }

            return [
                'success' => true,
                'message' => 'Email đã được gửi thành công',
                'to' => $to,
                'subject' => $subject
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi khi gửi email: ' . $e->getMessage(),
                'to' => $to,
                'subject' => $subject
            ];
        }
    }

    /**
     * Format content as HTML
     */
    protected function formatHtmlContent(string $subject, string $content, array $data = []): string
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . $subject . '</title></head><body>';
        $html .= '<h2>' . $subject . '</h2>';
        $html .= '<div>' . nl2br(e($content)) . '</div>';
        
        if (!empty($data)) {
            $html .= '<div style="margin-top: 20px; padding: 10px; background: #f5f5f5;">';
            $html .= '<h3>Thông tin bổ sung:</h3>';
            foreach ($data as $key => $value) {
                $html .= '<p><strong>' . ucfirst(str_replace('_', ' ', $key)) . ':</strong> ' . (is_array($value) ? json_encode($value) : $value) . '</p>';
            }
            $html .= '</div>';
        }
        
        $html .= '<div style="margin-top: 20px; font-size: 12px; color: #666;">';
        $html .= '<p>Email từ hệ thống ' . SystemConfigHelper::getName() . '</p>';
        $html .= '<p>Thời gian: ' . now()->format('d/m/Y H:i:s') . '</p>';
        $html .= '</div>';
        $html .= '</body></html>';
        
        return $html;
    }

    /**
     * Gửi email PHP thuần (nhanh nhất - không cần cấu hình SMTP)
     */
    public function sendRaw(string $to, string $subject, string $content): array
    {
        try {
            // Lấy thông tin from từ config
            $configs = $this->configService->getByGroup('email');
            $emailConfigs = [];
            foreach ($configs as $config) {
                $emailConfigs[$config['key']] = $config['value'];
            }
            
            $fromEmail = $emailConfigs['from_address'] ?? SystemConfigHelper::getAppEmail();
            $fromName = $emailConfigs['from_name'] ?? SystemConfigHelper::getName();

            // Tạo headers với From information
            $headers = [
                'From: ' . $fromName . ' <' . $fromEmail . '>',
                'Reply-To: ' . $fromEmail,
                'X-Mailer: PHP/' . phpversion(),
                'Content-Type: text/plain; charset=UTF-8'
            ];

            // Gửi email bằng PHP thuần với headers
            $result = mail($to, $subject, $content, implode("\r\n", $headers));

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Email đã được gửi thành công',
                    'to' => $to,
                    'subject' => $subject
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không thể gửi email',
                    'to' => $to,
                    'subject' => $subject
                ];
            }

        } catch (Exception $e) {
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
            return [
                'success' => false,
                'message' => 'Lỗi khi gửi email: ' . $e->getMessage()
            ];
        }
    }

}
