<?php

namespace App\Enums;

enum ConfigType: string
{
    case STRING = 'string';
    case INTEGER = 'integer';
    case BOOLEAN = 'boolean';
    case JSON = 'json';
    case ARRAY = 'array';
    case FLOAT = 'float';

    public function getLabel(): string
    {
        return match($this) {
            self::STRING => 'Chuỗi',
            self::INTEGER => 'Số nguyên',
            self::BOOLEAN => 'Boolean',
            self::JSON => 'JSON',
            self::ARRAY => 'Mảng',
            self::FLOAT => 'Số thực',
        };
    }

    public function getValidationRule(): string
    {
        return match($this) {
            self::STRING => 'string',
            self::INTEGER => 'integer',
            self::BOOLEAN => 'boolean',
            self::JSON => 'json',
            self::ARRAY => 'array',
            self::FLOAT => 'numeric',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'value' => $case->value,
                'label' => $case->getLabel(),
            ];
        })->toArray();
    }
}
