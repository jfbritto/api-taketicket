<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case RECEIVED = 'received';
    case OVERDUE = 'overdue';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';

    public function isTerminal(): bool
    {
        return in_array($this, [self::CONFIRMED, self::REFUNDED, self::FAILED]);
    }
}
