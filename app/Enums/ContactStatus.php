<?php

namespace App\Enums;

enum ContactStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    /**
     * Get all status values
     * 
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get status label in Vietnamese
     * 
     * @return string
     */
    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'Chờ xử lý',
            self::IN_PROGRESS => 'Đang xử lý',
            self::COMPLETED => 'Hoàn thành',
            self::CANCELLED => 'Đã hủy',
        };
    }

    /**
     * Get status color for UI
     * 
     * @return string
     */
    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::IN_PROGRESS => 'info',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    /**
     * Check if status is active
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        return in_array($this, [self::PENDING, self::IN_PROGRESS]);
    }
}
