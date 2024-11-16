<?php

namespace App\Dto\Company;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateCompanyDto
{
    public function __construct(
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $name,
    ) {}
}