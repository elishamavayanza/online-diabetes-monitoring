<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Point de données pour un graphique de tendance')]
class TrendPointDTO
{
    public function __construct(
        #[OA\Property(example: '2026-08-05')]
        public readonly string $date,
        #[OA\Property(example: 118.5)]
        public readonly float $value,
    ) {}
}
