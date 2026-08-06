<?php

namespace App\DTO\Response\Nutrition;

use App\Entity\Nutrition\MealItem;

class MealItemResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $mealId,
        public readonly string $foodId,
        public readonly string $portionGrams,
        public readonly ?string $breadUnits,
        public readonly \DateTimeImmutable $createdAt,
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
