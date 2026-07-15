<?php

namespace App\Enums;

enum DeliveryWarningSeverity: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Critical = 'critical';
}
