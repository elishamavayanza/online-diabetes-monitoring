<?php

namespace App\DTO\Response\Medical\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Statistiques agrégées sur une mesure')]
class ReportMeasurementStatsDTO
{
    public function __construct(
        #[OA\Property(example: 142.0, nullable: true)]
        public readonly ?float $average,
        #[OA\Property(example: 78.0, nullable: true)]
        public readonly ?float $minimum,
        #[OA\Property(example: 268.0, nullable: true)]
        public readonly ?float $maximum,
        #[OA\Property(example: 24)]
        public readonly int $count,
        #[OA\Property(example: 'mg/dL', nullable: true)]
        public readonly ?string $unit = null,
    ) {}
}
