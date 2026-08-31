<?php

namespace App\DTO\Response\Medical\Report;

use App\DTO\Response\Healthcare\Report\TrendSeriesDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Section poids / IMC du rapport de suivi')]
class WeightReportSectionDTO
{
    public function __construct(
        public readonly ReportMeasurementStatsDTO $weight,
        public readonly ReportMeasurementStatsDTO $bmi,
        public readonly ?TrendSeriesDTO $weightTrend,
        public readonly ?TrendSeriesDTO $bmiTrend,
    ) {}
}
