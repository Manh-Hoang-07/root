<?php

namespace App\Enums;

enum PostStatus: string
{
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Nháp',
            self::SCHEDULED => 'Lên lịch',
            self::PUBLISHED => 'Đã xuất bản',
            self::ARCHIVED => 'Đã lưu trữ',
        };
    }

    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }
}
