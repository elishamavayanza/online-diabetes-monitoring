<?php

namespace App\DTO\Response\Nutrition;

use App\Entity\Nutrition\Meal;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MealResponseDTO',
    title: 'MealResponseDTO',
    description: 'Structure de réponse pour un repas'
)]
class MealResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: 'Déjeuner équilibré', description: 'Nom du repas')]
        public readonly string $name,

        #[OA\Property(type: 'string', nullable: true, example: 'Salade composée...', description: 'Description')]
        public readonly ?string $description,

        #[OA\Property(type: 'string', nullable: true, example: 'LUNCH', description: 'Type de repas')]
        public readonly ?string $mealType,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T12:00:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
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
