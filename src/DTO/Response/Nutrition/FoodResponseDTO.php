<?php

namespace App\DTO\Response\Nutrition;

use App\Entity\Nutrition\Food;

class FoodResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $categoryId,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $photoUrl,
        public readonly string $caloriesPer100g,
        public readonly string $carbsPer100g,
        public readonly string $proteinPer100g,
        public readonly string $fatPer100g,
        public readonly ?string $createdById,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Food $food): self
    {
        return new self(
            id: (string) $food->getId(),
            categoryId: (string) $food->getCategory()?->getId(),
            name: $food->getName(),
            description: $food->getDescription(),
            photoUrl: $food->getPhotoUrl(),
            caloriesPer100g: $food->getCaloriesPer100g(),
            carbsPer100g: $food->getCarbsPer100g(),
            proteinPer100g: $food->getProteinPer100g(),
            fatPer100g: $food->getFatPer100g(),
            createdById: $food->getCreatedBy()?->getId() ? (string) $food->getCreatedBy()->getId() : null,
            createdAt: $food->getCreatedAt(),
            updatedAt: $food->getUpdatedAt()
        );
    }
}
