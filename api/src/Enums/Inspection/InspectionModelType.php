<?php

namespace App\Enums\Inspection;

enum InspectionModelType: string
{
    case CAR = 'car';
    case USER = 'user';

    public const REGEX = self::CAR->value . '|' . self::USER->value;
}
