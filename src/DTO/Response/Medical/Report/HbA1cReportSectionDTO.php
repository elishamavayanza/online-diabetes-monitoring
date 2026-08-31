<?php

namespace App\DTO\Response\Medical\Report;

use App\DTO\Response\Healthcare\Report\TrendSeriesDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Section HbA1c du rapport de suivi')]
class HbA1cReportSectionDTO
{
    public function __construct(
        public readonly ReportMeasurementStatsDTO $stats,
        public readonly ?TrendSeriesDTO $trend,
    ) {}
}
