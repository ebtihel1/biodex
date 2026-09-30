<?php

namespace App\Enums;

enum DonationStatus: string
{
    case Available = 'available';
    case Claimed = 'claimed';
    case Completed = 'completed';
}