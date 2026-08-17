<?php

namespace App\DTO\Request\Nutrition;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'MealRequestDTO',
    description: 'Structure de requête pour la création d’un repas'
)]
class MealRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(description: 'Nom du repas', type: 'string', example: 'Déjeuner équilibré', maxLength: 150)]
        public readonly string $name,

        #[Assert\Length(max: 5000)]
        #[OA\Property(description: 'Description', type: 'string', example: 'Salade composée et blanc de poulet.', nullable: true, maxLength: 5000)]
        public readonly ?string $description,

        #[Assert\NotBlank]
        #[OA\Property(description: 'Type de repas', type: 'string', example: 'LUNCH')]
        public readonly mixed $mealType,

        #[OA\Property(description: 'ID du patient (obligatoire si la requête est faite par un professionnel)', type: 'integer', example: 12, nullable: true)]
        public readonly ?int $patientId = null
    ) {}
}
