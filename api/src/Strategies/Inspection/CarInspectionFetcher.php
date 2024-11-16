<?php

namespace App\Strategies\Inspection;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CarInspectionFetcher implements InspectionFetcherInterface
{
    public function __construct(private readonly ServiceEntityRepository $inspectionRepository) {}

    public function findInspections(string $relatedId): array
    {
        return $this->inspectionRepository->findAllOfCar($relatedId);
    }
}
