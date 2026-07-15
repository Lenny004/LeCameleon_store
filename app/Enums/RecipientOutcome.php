<?php

namespace App\Enums;

enum RecipientOutcome: string
{
    case Accepted = 'accepted';
    case Refused = 'refused';
    case NoAnswer = 'no_answer';
    case WrongAddress = 'wrong_address';
    case Rescheduled = 'rescheduled';
    case LeftWithNeighbor = 'left_with_neighbor';
}
