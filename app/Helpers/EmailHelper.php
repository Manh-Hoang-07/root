<?php

namespace App\Helpers;

use App\Services\Core\Email\EmailService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class EmailHelper
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
     * Gửi email với cấu hình từ database
     */
    public static function send($mailable, $to = null)
    {
        // Đảm bảo cấu hình đã được load
        self::loadConfig();
        
        if ($to) {
            return Mail::to($to)->send($mailable);
        }
        
        return Mail::send($mailable);
    }


    /**
     * Gửi email (text hoặc HTML)
     */
    public static function send(string $to, string $subject, string $content, array $data = []): array
    {
        $emailService = App::make(\App\Services\Core\Email\EmailService::class);
        return $emailService->send($to, $subject, $content, $data);
    }

    /**
     * Gửi email PHP thuần (nhanh nhất)
     */
    public static function sendRaw(string $to, string $subject, string $content): array
    {
        $emailService = App::make(\App\Services\Core\Email\EmailService::class);
        return $emailService->sendRaw($to, $subject, $content);
    }

    /**
     * Gửi email với template từ database
     */
    public static function sendWithTemplate(string $to, string $templateCode, array $data = [], string $locale = 'vi'): array
    {
        $emailService = App::make(\App\Services\Core\Email\EmailService::class);
        return $emailService->sendWithTemplate($to, $templateCode, $data, $locale);
    }

}
