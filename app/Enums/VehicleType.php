<?php

namespace App\Enums;

enum VehicleType: string
{
    case Motorcycle = 'motorcycle';
    case Van = 'van';
    case Truck = 'truck';
    case Bicycle = 'bicycle';
    case Car = 'car';
}
