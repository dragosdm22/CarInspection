<?php

namespace App\Traits;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

trait UserAwareTrait {
    private Security $security;

    public function setSecurity(Security $security): void
    {
        $this->security = $security;
    }

    public function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }

    public function getUserCompanyId(): string
    {
        return $this->getUser()->getCompany()->getId();
    }

    public function getUserId(): string
    {
        return $this->getUser()->getId();
    }
}
