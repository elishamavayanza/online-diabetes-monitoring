<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Statistiques démographiques des patients de l\'organisation')]
class DemographicsReportDTO
{
    /**
     * @param DistributionItemDTO[] $genderDistribution
     * @param DistributionItemDTO[] $ageGroups
     */
    public function __construct(
        public readonly StatisticValueDTO $totalPatients,
        public readonly StatisticValueDTO $activePatients,
        public readonly StatisticValueDTO $newPatients,
        public readonly array $genderDistribution,
        public readonly array $ageGroups,
    ) {}
}
