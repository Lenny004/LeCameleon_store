<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case Pending = 'pending';
    case Scheduled = 'scheduled';
    case Shipped = 'shipped';
    case PickedUp = 'picked_up';
    case InTransit = 'in_transit';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Failed = 'failed';
    case Returned = 'returned';
    case ReturnedToWarehouse = 'returned_to_warehouse';
    case Cancelled = 'cancelled';
}
