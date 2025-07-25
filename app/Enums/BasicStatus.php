<?php

namespace App\Enums;

enum BasicStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    /**
     * Lấy tên hiển thị của trạng thái
     */
    public function label(): string
    {
        return match($this) {
            self::Active => 'Hoạt động',
            self::Inactive => 'Không hoạt động',
        };
    }

    /**
     * Lấy danh sách các trạng thái dưới dạng mảng
     */
    public static function toArray(): array
    {
        return [
            ['id' => self::Active->value, 'name' => self::Active->label()],
            ['id' => self::Inactive->value, 'name' => self::Inactive->label()],
        ];
    }
} 