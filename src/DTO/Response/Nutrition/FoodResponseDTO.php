<?php

namespace App\DTO\Response\Nutrition;

use App\Entity\Nutrition\Food;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FoodResponseDTO',
    title: 'FoodResponseDTO',
    description: 'Structure de réponse pour un aliment'
)]
class FoodResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '55ee6677-8899-0011-2233-445566778899', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID de la catégorie')]
        public readonly string $categoryId,

        #[OA\Property(type: 'string', example: 'Pomme', description: 'Nom de l’aliment')]
        public readonly string $name,

        #[OA\Property(type: 'string', nullable: true, example: 'Fruit frais', description: 'Description')]
        public readonly ?string $description,

        #[OA\Property(type: 'string', format: 'uri', nullable: true, example: 'https://example.com/apple.jpg', description: 'URL de la photo')]
        public readonly ?string $photoUrl,

        #[OA\Property(type: 'string', example: '52.00', description: 'Calories pour 100g')]
        public readonly string $caloriesPer100g,

        #[OA\Property(type: 'string', example: '14.00', description: 'Glucides pour 100g')]
        public readonly string $carbsPer100g,

        #[OA\Property(type: 'string', example: '0.30', description: 'Protéines pour 100g')]
        public readonly string $proteinPer100g,

        #[OA\Property(type: 'string', example: '0.20', description: 'Lipides pour 100g')]
        public readonly string $fatPer100g,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID du créateur')]
        public readonly ?string $createdById,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
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
