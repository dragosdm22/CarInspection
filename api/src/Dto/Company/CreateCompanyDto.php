<?php

namespace App\Dto\Company;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateCompanyDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public string $name,
    ) {}
}