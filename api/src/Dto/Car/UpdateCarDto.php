<?php

namespace App\Dto\Car;

use App\Enums\Car\CarType;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateCarDto
{
    public function __construct(
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $licensePlate,
        #[Assert\Choice(
            choices: [
                CarType::SMALL_CAR->value,
                CarType::MEDIUM_CAR->value,
                CarType::LARGE_CAR->value,
            ],
            message: 'Choose one of (small) 1, (medium) 2, (large) 3',
        )]
        public ?int $carType
    ) {}
}