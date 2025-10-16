<?php

namespace App\Enums;

enum AttributeType: string
{
    case TEXT = 'text';
    case SELECT = 'select';
    case MULTISELECT = 'multiselect';
    case COLOR = 'color';
    case IMAGE = 'image';

    public function label(): string
    {
        return match($this) {
            self::TEXT => 'Văn bản',
            self::SELECT => 'Chọn một',
            self::MULTISELECT => 'Chọn nhiều',
            self::COLOR => 'Màu sắc',
            self::IMAGE => 'Hình ảnh',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::TEXT => 'fas fa-font',
            self::SELECT => 'fas fa-list',
            self::MULTISELECT => 'fas fa-list-check',
            self::COLOR => 'fas fa-palette',
            self::IMAGE => 'fas fa-image',
        };
    }
}
