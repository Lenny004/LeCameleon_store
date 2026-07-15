<?php

namespace App\Enums;

enum ReturnRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Denied = 'denied';
    case Refunded = 'refunded';
}
