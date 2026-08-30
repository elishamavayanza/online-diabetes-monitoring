<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Nutrition et activité physique')]
class LifestyleReportDTO
{
    /**
     * @param DistributionItemDTO[] $mealsByType
     */
    public function __construct(
        public readonly StatisticValueDTO $totalMeals,
        public readonly array $mealsByType,
        public readonly StatisticValueDTO $physicalActivitySessions,
        public readonly StatisticValueDTO $totalActivityMinutes,
        public readonly StatisticValueDTO $averageActivityMinutes,
    ) {}
}
