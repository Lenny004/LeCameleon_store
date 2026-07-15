<?php

namespace App\Enums;

enum WarningAppliesTo: string
{
    case Checkout = 'checkout';
    case Tracking = 'tracking';
    case Admin = 'admin';
}
