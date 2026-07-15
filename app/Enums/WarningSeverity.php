<?php

namespace App\Enums;

enum WarningSeverity: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Danger = 'danger';
}
