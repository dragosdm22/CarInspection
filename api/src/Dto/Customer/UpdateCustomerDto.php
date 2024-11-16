<?php

namespace App\Dto\Customer;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateCustomerDto
{
    public function __construct(
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $phoneNumber,
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $firstName,
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $lastName,
    ) {}
}