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
            ['id' => self::Male->value, 'name' => self::Male->label()],
            ['id' => self::Female->value, 'name' => self::Female->label()],
            ['id' => self::Other->value, 'name' => self::Other->label()],
        ];
    }
} 