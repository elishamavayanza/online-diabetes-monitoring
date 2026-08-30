<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Série temporelle pour graphique')]
class TrendSeriesDTO
{
    /**
     * @param TrendPointDTO[] $points
     */
    public function __construct(
        #[OA\Property(example: 'Glycémie moyenne')]
        public readonly string $label,
        #[OA\Property(example: 'mg/dL')]
        public readonly ?string $unit,
        public readonly array $points,
    ) {}
}
