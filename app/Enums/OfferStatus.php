<?php

namespace App\Enums;

enum OfferStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Declined = 'declined';
    case Countered = 'countered';
    case Expired = 'expired';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Accepted => 'Aceptada',
            self::Declined => 'Rechazada',
            self::Countered => 'Contraoferta',
            self::Expired => 'Vencida',
            self::Withdrawn => 'Retirada',
        };
    }
}
