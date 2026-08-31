<?php

namespace App\DTO\Response\Medical\Report;

use App\DTO\Response\Healthcare\Report\DistributionItemDTO;
use App\DTO\Response\Healthcare\Report\TrendSeriesDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Section glycémie du rapport de suivi')]
class GlucoseReportSectionDTO
{
    /**
     * @param DistributionItemDTO[] $ranges
     */
    public function __construct(
        public readonly ReportMeasurementStatsDTO $stats,
        public readonly array $ranges,
        public readonly ?TrendSeriesDTO $trend,
    ) {}
}
