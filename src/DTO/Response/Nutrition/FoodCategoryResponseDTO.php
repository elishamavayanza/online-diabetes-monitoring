<?php

namespace App\DTO\Response\Nutrition;

use App\Entity\Nutrition\FoodCategory;

class FoodCategoryResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $label,
        public readonly ?string $description,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(FoodCategory $category): self
    {
        return new self(
            id: (string) $category->getId(),
            label: $category->getLabel(),
            description: $category->getDescription(),
            createdAt: $category->getCreatedAt(),
            updatedAt: $category->getUpdatedAt()
        );
    }
}
