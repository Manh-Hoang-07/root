<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Draft = 'draft';

    /**
     * Lấy tên hiển thị của trạng thái
     */
    public function label(): string
    {
        return match($this) {
            self::Active => 'Đang bán',
            self::Inactive => 'Ngừng bán',
            self::Draft => 'Bản nháp',
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
            ['id' => self::Draft->value, 'name' => self::Draft->label()],
        ];
    }
} 