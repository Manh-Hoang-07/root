<?php

namespace App\Enums;

enum ConfigGroup: string
{
    case GENERAL = 'general';
    case EMAIL = 'email';
    case DATABASE = 'database';
    case STORAGE = 'storage';
    case SECURITY = 'security';
    case API = 'api';
    case CACHE = 'cache';
    case NOTIFICATION = 'notification';
    case PAYMENT = 'payment';
    case EMAIL_TEMPLATES = 'email_templates';
    case CUSTOM = 'custom';

    public function getLabel(): string
    {
        return match($this) {
            self::GENERAL => 'Cài đặt chung',
            self::EMAIL => 'Cấu hình Email',
            self::DATABASE => 'Cài đặt Database',
            self::STORAGE => 'Cấu hình lưu trữ',
            self::SECURITY => 'Cài đặt bảo mật',
            self::API => 'Cài đặt API',
            self::CACHE => 'Cài đặt Cache',
            self::NOTIFICATION => 'Cài đặt thông báo',
            self::PAYMENT => 'Cài đặt thanh toán',
            self::EMAIL_TEMPLATES => 'Mẫu email',
            self::CUSTOM => 'Cài đặt tùy chỉnh',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::GENERAL => 'Các cài đặt cơ bản của hệ thống',
            self::EMAIL => 'Cấu hình gửi email và SMTP',
            self::DATABASE => 'Cài đặt kết nối và tối ưu database',
            self::STORAGE => 'Cấu hình lưu trữ file và media',
            self::SECURITY => 'Cài đặt bảo mật và xác thực',
            self::API => 'Cài đặt API và rate limiting',
            self::CACHE => 'Cấu hình cache và performance',
            self::NOTIFICATION => 'Cài đặt thông báo push và email',
            self::PAYMENT => 'Cấu hình thanh toán và gateway',
            self::EMAIL_TEMPLATES => 'Mẫu email và template thông báo',
            self::CUSTOM => 'Cài đặt tùy chỉnh do người dùng định nghĩa',
        };
    }

    public function isPublic(): bool
    {
        return match($this) {
            self::GENERAL => true,
            self::API => true,
            self::CACHE => false,
            self::EMAIL => false,
            self::DATABASE => false,
            self::STORAGE => false,
            self::SECURITY => false,
            self::NOTIFICATION => false,
            self::PAYMENT => false,
            self::EMAIL_TEMPLATES => false,
            self::CUSTOM => true,
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'value' => $case->value,
                'label' => $case->getLabel(),
                'description' => $case->getDescription(),
                'is_public' => $case->isPublic(),
            ];
        })->toArray();
    }
}

