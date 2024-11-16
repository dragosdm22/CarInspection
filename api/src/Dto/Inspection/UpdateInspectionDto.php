<?php

namespace App\Dto\Inspection;

use App\Enums\Inspection\InspectionType;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateInspectionDto
{
    public function __construct(
        #[Assert\Choice(
            choices: [
                InspectionType::SIX_MONTHS->value,
                InspectionType::ONE_YEAR->value,
                InspectionType::TWO_YEARS->value,
            ],
            message: 'Choose one of (six months) 1, (one year) 2, (two years) 3',
        )]
        public ?int $inspectionType,
        #[Assert\Date]
        public ?\DateTimeInterface $inspectionDate,
    ) {}
}