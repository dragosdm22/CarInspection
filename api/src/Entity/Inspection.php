<?php

namespace App\Entity;

use App\Repository\InspectionRepository;
use App\Traits\TimestampedEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: InspectionRepository::class)]
#[HasLifecycleCallbacks]
class Inspection
{
    use TimestampedEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $inspectionType = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $inspectionDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getInspectionType(): ?int
    {
        return $this->inspectionType;
    }

    public function setInspectionType(int $inspectionType): static
    {
        $this->inspectionType = $inspectionType;

        return $this;
    }

    public function getInspectionDate(): ?\DateTimeInterface
    {
        return $this->inspectionDate;
    }

    public function setInspectionDate(\DateTimeInterface $inspectionDate): static
    {
        $this->inspectionDate = $inspectionDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }
}
