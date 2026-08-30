<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Valeur statistique avec comparaison à la période précédente')]
class StatisticValueDTO
{
    public function __construct(
        #[OA\Property(example: 142)]
        public readonly ?float $value,
        #[OA\Property(example: 128)]
        public readonly ?float $previousValue = null,
        #[OA\Property(example: 10.9, description: 'Évolution en pourcentage par rapport à la période précédente')]
        public readonly ?float $changePercent = null,
        #[OA\Property(example: 'mg/dL')]
        public readonly ?string $unit = null,
    ) {}
}
