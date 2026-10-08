<?php

namespace App\Enums;

enum RelationType: string
{
    case Related = 'related';
    case Upsell = 'upsell';
    case CrossSell = 'cross_sell';
    case Similar = 'similar';
}
