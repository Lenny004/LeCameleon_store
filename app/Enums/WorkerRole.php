<?php

namespace App\Enums;

enum WorkerRole: string
{
    case Driver = 'driver';
    case Collector = 'collector';
    case Dispatcher = 'dispatcher';
    case Manager = 'manager';
}
