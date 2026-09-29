<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    /**
     * Get all values as an array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
