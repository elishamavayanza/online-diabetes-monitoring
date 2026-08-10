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
        #[OA\Property(type: 'string', maxLength: 150, example: 'Déjeuner équilibré', description: 'Nom du repas')]
        public readonly string $name,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Salade composée et blanc de poulet.', description: 'Description')]
        public readonly ?string $description,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'LUNCH', description: 'Type de repas')]
        public readonly mixed $mealType
    ) {}
}
