<?php

namespace App\Libraries\Core;

use App\Services\Core\Email\EmailService;
use Illuminate\Support\Facades\App;

class Email
{
    /**
     * Lấy instance của EmailService
     */
    public static function getService(): EmailService
    {
        return App::make(EmailService::class);
    }

    /**
     * Load cấu hình email từ database
     */
    public static function loadConfig(): bool
    {
        return self::getService()->getConfig();
    }

    /**
     * Gửi email (text hoặc HTML)
     */
    public static function send(string $to, string $subject, string $content, array $data = []): array
    {
        return self::getService()->send($to, $subject, $content, $data);
    }

    /**
     * Gửi email PHP thuần (nhanh nhất)
     */
    public static function sendRaw(string $to, string $subject, string $content): array
    {
        return self::getService()->sendRaw($to, $subject, $content);
    }

    /**
     * Gửi email với template từ database
     */
    public static function sendWithTemplate(string $to, string $templateCode, array $data = [], string $locale = 'vi'): array
    {
        return self::getService()->sendWithTemplate($to, $templateCode, $data, $locale);
    }

}
