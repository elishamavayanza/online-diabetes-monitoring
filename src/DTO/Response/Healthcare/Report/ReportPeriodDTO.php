<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Période analysée et période de comparaison')]
class ReportPeriodDTO
{
    public function __construct(
        #[OA\Property(example: '2026-08-01')]
        public readonly string $from,
        #[OA\Property(example: '2026-08-30')]
        public readonly string $to,
        #[OA\Property(example: '2026-07-02')]
        public readonly string $previousFrom,
        #[OA\Property(example: '2026-07-31')]
        public readonly string $previousTo,
        #[OA\Property(example: 'month', enum: ['custom', 'month', 'quarter', 'year'])]
        public readonly string $preset,
    ) {}
}
