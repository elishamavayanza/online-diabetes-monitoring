<?php

namespace App\DTO\Request\Nutrition;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'FoodRequestDTO',
    description: 'Structure de requête pour la création d’un aliment'
)]
class FoodRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID de la catégorie')]
        public readonly string $categoryId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'Pomme', description: 'Nom de l’aliment')]
        public readonly string $name,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Fruit frais', description: 'Description')]
        public readonly ?string $description,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://example.com/apple.jpg', description: 'URL de la photo')]
        public readonly ?string $photoUrl,

        #[Assert\NotBlank]
        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '52.00', description: 'Calories pour 100g')]
        public readonly string $caloriesPer100g,

        #[Assert\NotBlank]
        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '14.00', description: 'Glucides pour 100g')]
        public readonly string $carbsPer100g,

        #[Assert\NotBlank]
        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '0.30', description: 'Protéines pour 100g')]
        public readonly string $proteinPer100g,

        #[Assert\NotBlank]
        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '0.20', description: 'Lipides pour 100g')]
        public readonly string $fatPer100g,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID du créateur')]
        public readonly ?string $createdById
    ) {}
}
