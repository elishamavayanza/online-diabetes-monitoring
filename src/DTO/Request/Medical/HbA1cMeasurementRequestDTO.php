<?php

namespace App\DTO\Request\Medical;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'HbA1cMeasurementRequestDTO',
    description: 'Structure de requête pour l’enregistrement d’une mesure d’HbA1c'
)]
class HbA1cMeasurementRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '6.5', description: 'Valeur de l’HbA1c en pourcentage')]
        public readonly string $valuePercent
    ) {}
}
