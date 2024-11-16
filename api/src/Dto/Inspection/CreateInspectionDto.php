<?php

namespace App\Dto\Inspection;

use App\Enums\Inspection\InspectionType;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateInspectionDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(
            choices: [
                InspectionType::SIX_MONTHS->value,
                InspectionType::ONE_YEAR->value,
                InspectionType::TWO_YEARS->value,
            ],
            message: 'Choose one of (six months) 1, (one year) 2, (two years) 3',
        )]
        public int $inspectionType,
        #[Assert\NotBlank]
        #[Assert\Date]
        public string $inspectionDate,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $carId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId
    ) {}
}