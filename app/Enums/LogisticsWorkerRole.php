<?php

namespace App\Enums;

enum LogisticsWorkerRole: string
{
    case Collector = 'collector';
    case Driver = 'driver';
    case Dispatcher = 'dispatcher';
    case Supervisor = 'supervisor';
}
