<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Indicateurs clés et tendances')]
class TrendsReportDTO
{
    /**
     * @param TrendSeriesDTO[] $series
     */
    public function __construct(
        public readonly StatisticValueDTO $patientsWithMeasurements,
        public readonly StatisticValueDTO $measurementComplianceRate,
        public readonly array $series,
    ) {}
}
