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
        #[OA\Property(type: 'string', example: 'GENERAL', description: 'Classe du médicament (INSULIN ou GENERAL)')]
        public readonly mixed $category,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Antidiabétique...', description: 'Description')]
        public readonly ?string $description,

        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, nullable: true, example: 'PharmaLab', description: 'Fabricant')]
        public readonly ?string $manufacturer,

        #[OA\Property(type: 'string', nullable: true, example: 'LIQUID', description: 'Forme galénique (TABLET ou LIQUID, obligatoire si la classe est GENERAL)')]
        public readonly mixed $form,

        #[OA\Property(type: 'string', nullable: true, example: 'LONG_ACTING', description: 'Type d’insuline (obligatoire si la classe est INSULIN)')]
        public readonly mixed $insulinType,

        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', maxLength: 50, nullable: true, example: 'U-100', description: 'Concentration de l’insuline (obligatoire si la classe est INSULIN)')]
        public readonly ?string $concentration,

        #[OA\Property(type: 'boolean', example: true, description: 'Médicament actif dans le référentiel')]
        public readonly bool $active = true
    ) {}
}