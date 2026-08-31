<?php

namespace App\DTO\Response\Medical\Report;

use App\DTO\Response\Healthcare\Report\TrendSeriesDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Section tension artérielle du rapport de suivi')]
class BloodPressureReportSectionDTO
{
    /**
     * @param TrendSeriesDTO[] $trends
     */
    public function __construct(
        public readonly ReportMeasurementStatsDTO $systolic,
        public readonly ReportMeasurementStatsDTO $diastolic,
        public readonly array $trends,
    ) {}
}
