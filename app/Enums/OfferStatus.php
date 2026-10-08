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
}
