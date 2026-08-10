<?php

namespace App\DTO\Request\Nutrition;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'MealItemRequestDTO',
    description: 'Structure de requête pour l’ajout d’un aliment à un repas'
)]
class MealItemRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du repas')]
        public readonly string $mealId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID de l’aliment')]
        public readonly string $foodId,

        #[Assert\NotBlank]
        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '150.00', description: 'Portion en grammes')]
        public readonly string $portionGrams,

        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', nullable: true, example: '1.25', description: 'Unités pain')]
        public readonly ?string $breadUnits
    ) {}
}
