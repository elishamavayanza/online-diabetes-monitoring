<?php

namespace App\DTO\Response\Medical\Report;

use App\DTO\Response\Healthcare\Report\TrendSeriesDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Section activité physique du rapport de suivi')]
class PhysicalActivityReportSectionDTO
{
    public function __construct(
        #[OA\Property(example: 12)]
        public readonly int $sessions,
        #[OA\Property(example: 480)]
        public readonly int $totalMinutes,
        #[OA\Property(example: 40.0, nullable: true)]
        public readonly ?float $averageMinutes,
        public readonly ?TrendSeriesDTO $trend,
    ) {}
}
