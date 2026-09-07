<?php

namespace App\DTO\Request\Treatment;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'InsulinRequestDTO',
    description: 'Structure de requête pour la création ou la mise à jour d’une insuline'
)]
class InsulinRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '11bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du médicament associé (catégorie INSULIN)')]
        public readonly string $medicationId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'LONG_ACTING', description: 'Type d’insuline')]
        public readonly mixed $insulinType,

        #[Assert\NotBlank]
        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', maxLength: 50, example: 'U-100', description: 'Concentration de l’insuline')]
        public readonly string $concentration
    ) {}
}