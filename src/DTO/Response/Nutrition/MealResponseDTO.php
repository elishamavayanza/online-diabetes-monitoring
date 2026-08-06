<?php

namespace App\DTO\Response\Nutrition;

use App\Entity\Nutrition\Meal;

class MealResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $mealType,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Meal $meal): self
    {
        return new self(
            id: (string) $meal->getId(),
            name: $meal->getName(),
            description: $meal->getDescription(),
            mealType: $meal->getMealType()?->value,
            createdAt: $meal->getCreatedAt(),
            updatedAt: $meal->getUpdatedAt()
        );
    }
}
