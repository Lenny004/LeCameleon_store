<?php

namespace App\Enums;

enum ConditionGrade: string
{
    case Mint = 'mint';
    case Excellent = 'excellent';
    case Good = 'good';
    case Fair = 'fair';
    case Poor = 'poor';
}
