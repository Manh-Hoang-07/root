<?php

namespace App\Enums;

enum ConfigGroup: string
{
    case GENERAL = 'general';
    case EMAIL = 'email';

    public function getLabel(): string
    {
        return match($this) {
            self::GENERAL => 'Cài đặt chung',
            self::EMAIL => 'Cấu hình Email',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::GENERAL => 'Các cài đặt cơ bản của hệ thống',
            self::EMAIL => 'Cấu hình gửi email và SMTP',
        };
    }

    public function isPublic(): bool
    {
        return match($this) {
            self::GENERAL => true,
            self::EMAIL => false,
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

