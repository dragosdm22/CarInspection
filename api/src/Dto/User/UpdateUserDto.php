<?php

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateUserDto
{
    public function __construct(
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $username,
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $firstName,
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $lastName,
    ) {}
}