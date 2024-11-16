<?php

namespace App\Enums\Car;

enum CarType: int
{
    // This should reflect the expiry date of the inspection, maybe
    case SMALL_CAR = 1;
    case MEDIUM_CAR = 2;
    case LARGE_CAR = 3;
}