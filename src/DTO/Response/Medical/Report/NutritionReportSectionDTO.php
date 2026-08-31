<?php

namespace App\DTO\Response\Medical\Report;

use App\DTO\Response\Healthcare\Report\DistributionItemDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Section nutrition du rapport de suivi')]
class NutritionReportSectionDTO
{
    /**
     * @param DistributionItemDTO[] $mealsByType
     */
    public function __construct(
        #[OA\Property(example: 45)]
        public readonly int $totalMeals,
        public readonly array $mealsByType,
    ) {}
}
