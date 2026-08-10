<?php

namespace App\DTO\Request\Nutrition;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'FoodCategoryRequestDTO',
    description: 'Structure de requête pour la création d’une catégorie d’aliments'
)]
class FoodCategoryRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'Fruits et Légumes', description: 'Libellé de la catégorie')]
        public readonly string $label,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Aliments frais riches en nutriments.', description: 'Description')]
        public readonly ?string $description
    ) {}
}
