<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Élément de répartition (genre, tranche d\'âge, statut…)')]
class DistributionItemDTO
{
    public function __construct(
        #[OA\Property(example: 'FEMALE')]
        public readonly string $label,
        #[OA\Property(example: 58)]
        public readonly int $count,
        #[OA\Property(example: 40.8)]
        public readonly float $percentage,
    ) {}
}
