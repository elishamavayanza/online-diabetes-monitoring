<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Statistiques de traitements et observance')]
class TreatmentReportDTO
{
    /**
     * @param DistributionItemDTO[] $intakesByStatus
     */
    public function __construct(
        public readonly StatisticValueDTO $activePrescriptions,
        public readonly StatisticValueDTO $newPrescriptions,
        public readonly StatisticValueDTO $adherenceRate,
        public readonly StatisticValueDTO $totalIntakes,
        public readonly array $intakesByStatus,
    ) {}
}
