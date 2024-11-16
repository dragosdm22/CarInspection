<?php

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateUserDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public string $username,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 6, max: 255)]
        public string $password,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public string $firstName,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public string $lastName,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $companyId
    ) {}
}