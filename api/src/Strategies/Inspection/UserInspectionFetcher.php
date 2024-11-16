<?php

namespace App\Strategies\Inspection;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class UserInspectionFetcher implements InspectionFetcherInterface
{
    public function __construct(private readonly ServiceEntityRepository $inspectionRepository) {}

    public function findInspections(string $relatedId): array
    {
        return $this->inspectionRepository->findAllOfUser($relatedId);
    }
}
