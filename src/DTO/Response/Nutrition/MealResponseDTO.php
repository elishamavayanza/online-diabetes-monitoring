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
        #[OA\Property(description: 'Identifiant unique', type: 'string', example: '1')]
        public readonly string $id,

        #[OA\Property(description: 'Nom du repas', type: 'string', example: 'Déjeuner équilibré')]
        public readonly string $name,

        #[OA\Property(description: 'Description', type: 'string', example: 'Salade composée...', nullable: true)]
        public readonly ?string $description,

        #[OA\Property(description: 'Type de repas', type: 'string', example: 'LUNCH', nullable: true)]
        public readonly ?string $mealType,

        #[OA\Property(description: 'Date et heure de la mesure/repas', type: 'string', format: 'date-time', example: '2026-08-17T12:00:00Z', nullable: true)]
        public readonly ?\DateTimeImmutable $measuredAt,

        #[OA\Property(description: 'ID du patient associé', type: 'integer', example: 12, nullable: true)]
        public readonly ?int $patientId,

        #[OA\Property(description: 'Date de création', type: 'string', format: 'date-time', example: '2026-08-10T12:00:00Z')]
        public readonly ?\DateTimeImmutable $createdAt,

        #[OA\Property(description: 'Date de mise à jour', type: 'string', format: 'date-time', example: null, nullable: true)]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Meal $meal): self
    {
        return new self(
            id: (string) $meal->getId(),
            name: $meal->getName(),
            description: $meal->getDescription(),
            mealType: $meal->getMealType()?->value,
            measuredAt: method_exists($meal, 'getMeasuredAt') ? $meal->getMeasuredAt() : null,
            patientId: $meal->getPatient()?->getId(),
            createdAt: method_exists($meal, 'getCreatedAt') ? $meal->getCreatedAt() : null,
            updatedAt: method_exists($meal, 'getUpdatedAt') ? $meal->getUpdatedAt() : null
        );
    }
}
