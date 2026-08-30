<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Indicateurs d\'état de santé agrégés')]
class HealthStatusReportDTO
{
    /**
     * @param DistributionItemDTO[] $glucoseRanges
     */
    public function __construct(
        public readonly StatisticValueDTO $averageGlucose,
        public readonly StatisticValueDTO $glucoseMeasurements,
        public readonly array $glucoseRanges,
        public readonly StatisticValueDTO $averageHbA1c,
        public readonly StatisticValueDTO $hba1cMeasurements,
        public readonly StatisticValueDTO $averageSystolic,
        public readonly StatisticValueDTO $averageDiastolic,
        public readonly StatisticValueDTO $averageBmi,
        public readonly StatisticValueDTO $averageWeightKg,
    ) {}
}
