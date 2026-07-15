<?php

namespace App\Enums;

enum InventoryMovementType: string
{
    case StockIn = 'stock_in';
    case StockOut = 'stock_out';
    case Reserve = 'reserve';
    case Release = 'release';
    case Adjust = 'adjust';
    case Return = 'return';
}
