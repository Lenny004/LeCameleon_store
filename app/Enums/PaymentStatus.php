<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Authorized = 'authorized';
    case Captured = 'captured';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Authorized => 'Autorizado',
            self::Captured => 'Capturado',
            self::Failed => 'Fallido',
            self::Refunded => 'Reembolsado',
        };
    }
}
