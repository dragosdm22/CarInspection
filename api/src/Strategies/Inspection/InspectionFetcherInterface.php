<?php

namespace App\Strategies\Inspection;

interface InspectionFetcherInterface
{
    public function findInspections(string $relatedId): array;
}
