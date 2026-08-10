<?php

namespace App\DTO\Request\Medical;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'BloodGlucoseMeasurementRequestDTO',
    description: 'Structure de requête pour l’enregistrement d’une mesure de glycémie'
)]
class BloodGlucoseMeasurementRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '1.26', description: 'Valeur de la glycémie (format décimal)')]
        public readonly string $value,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'g/L', description: 'Unité de mesure')]
        public readonly mixed $unit,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'FASTING', description: 'Contexte de la mesure')]
        public readonly mixed $context
    ) {}
}
