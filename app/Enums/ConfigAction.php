<?php

namespace App\Enums;

enum ConfigAction: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case RESTORED = 'restored';

    public function getLabel(): string
    {
        return match($this) {
            self::CREATED => 'Tạo mới',
            self::UPDATED => 'Cập nhật',
            self::DELETED => 'Xóa',
            self::RESTORED => 'Khôi phục',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::CREATED => 'success',
            self::UPDATED => 'info',
            self::DELETED => 'danger',
            self::RESTORED => 'warning',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'value' => $case->value,
                'label' => $case->getLabel(),
                'color' => $case->getColor(),
            ];
        })->toArray();
    }
}

