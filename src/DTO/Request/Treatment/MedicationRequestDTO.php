<?php

namespace App\DTO\Request\Treatment;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'MedicationRequestDTO',
    description: 'Structure de requête pour la création d’un médicament'
)]
class MedicationRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'Paracétamol 500mg', description: 'Nom du médicament')]
        public readonly string $name,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'ANALGESIC', description: 'Catégorie')]
        public readonly mixed $category,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Antalgique et antipyrétique.', description: 'Description')]
        public readonly ?string $description,

        #[Assert\PositiveOrZero]
        #[OA\Property(type: 'integer', minimum: 0, nullable: true, example: 0, description: 'Niveau d’insuline')]
        public readonly ?int $insulinLevel,

        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, nullable: true, example: 'PharmaLab', description: 'Fabricant')]
        public readonly ?string $manufacturer
    ) {}
}
