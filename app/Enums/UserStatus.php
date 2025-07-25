<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Inactive = 'inactive';

    /**
     * Lấy tên hiển thị của trạng thái
     */
    public function label(): string
    {
        return match($this) {
            self::Active => 'Hoạt động',
            self::Pending => 'Chờ xác nhận',
            self::Inactive => 'Đã khóa',
        };
    }

    /**
     * Lấy danh sách các trạng thái dưới dạng mảng
     */
    public static function toArray(): array
    {
        return [
            ['id' => self::Active->value, 'name' => self::Active->label()],
            ['id' => self::Pending->value, 'name' => self::Pending->label()],
            ['id' => self::Inactive->value, 'name' => self::Inactive->label()],
        ];
    }
} 