<?php

namespace App\DTO\Response\Nutrition;

use App\Entity\Nutrition\MealItem;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'MealItemResponseDTO',
    description: 'Structure de réponse pour un élément de repas'
)]
class MealItemResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '55ee6677-8899-0011-2233-445566778899', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du repas')]
        public readonly string $mealId,

        #[OA\Property(type: 'string', format: 'uuid', example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID de l’aliment')]
        public readonly string $foodId,

        #[OA\Property(type: 'string', example: '150.00', description: 'Portion en grammes')]
        public readonly string $portionGrams,

        #[OA\Property(type: 'string', nullable: true, example: '1.25', description: 'Unités pain')]
        public readonly ?string $breadUnits,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T12:00:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(MealItem $mealItem): self
    {
        return new self(
            id: (string) $mealItem->getId(),
            mealId: (string) $mealItem->getMeal()?->getId(),
            foodId: (string) $mealItem->getFood()?->getId(),
            portionGrams: $mealItem->getPortionGrams(),
            breadUnits: $mealItem->getBreadUnits(),
            createdAt: $mealItem->getCreatedAt(),
            updatedAt: $mealItem->getUpdatedAt()
        );
    }
}
