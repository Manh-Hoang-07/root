<?php

namespace App\Enums;

enum Gender: int
{
    case Male = 1;
    case Female = 2;
    case Other = 3;

    /**
     * Lấy tên hiển thị của giới tính
     */
    public function label(): string
    {
        return match($this) {
            self::Male => 'Nam',
            self::Female => 'Nữ',
            self::Other => 'Khác',
        };
    }

    /**
     * Lấy danh sách các giới tính dưới dạng mảng
     */
    public static function toArray(): array
    {
        return [
            self::Male->value => self::Male->label(),
            self::Female->value => self::Female->label(),
            self::Other->value => self::Other->label(),
        ];
    }
} 