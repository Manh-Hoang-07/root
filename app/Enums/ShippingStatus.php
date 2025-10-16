<?php

namespace App\Enums;

enum ShippingStatus: string
{
    case PENDING = 'pending';
    case PREPARING = 'preparing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case RETURNED = 'returned';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Chờ vận chuyển',
            self::PREPARING => 'Đang chuẩn bị',
            self::SHIPPED => 'Đã giao hàng',
            self::DELIVERED => 'Đã nhận hàng',
            self::RETURNED => 'Đã trả hàng',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::PREPARING => 'info',
            self::SHIPPED => 'primary',
            self::DELIVERED => 'success',
            self::RETURNED => 'danger',
        };
    }
}
