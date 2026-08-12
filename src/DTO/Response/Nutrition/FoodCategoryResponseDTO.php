<?php

namespace App\DTO\Response\Nutrition;

use App\Entity\Nutrition\FoodCategory;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FoodCategoryResponseDTO',
    title: 'FoodCategoryResponseDTO',
    description: 'Structure de réponse pour une catégorie d’aliments'
)]
class FoodCategoryResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: 'Fruits et Légumes', description: 'Libellé')]
        public readonly string $label,

        #[OA\Property(type: 'string', nullable: true, example: 'Aliments frais...', description: 'Description')]
        public readonly ?string $description,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
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
