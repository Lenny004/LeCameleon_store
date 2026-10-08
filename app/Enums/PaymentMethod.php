<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Transfer = 'transfer';
    case Cod = 'cod';
    case Stripe = 'stripe';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Transfer => 'Transferencia bancaria',
            self::Cod => 'Pago contra entrega',
            self::Stripe => 'Tarjeta',
            self::Manual => 'Pago manual',
        };
    }

    public function isManual(): bool
    {
        return in_array($this, [self::Transfer, self::Cod, self::Manual], true);
    }

    public function isOnline(): bool
    {
        return $this === self::Stripe;
    }
}
