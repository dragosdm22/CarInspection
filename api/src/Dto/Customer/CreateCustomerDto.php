<?php

namespace App\Dto\Customer;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateCustomerDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public string $phoneNumber,
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $firstName,
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $lastName,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $companyId
    ) {}
}