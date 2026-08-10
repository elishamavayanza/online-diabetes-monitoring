<?php

namespace App\DTO\Request\Medical;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'WeightMeasurementRequestDTO',
    description: 'Structure de requête pour l’enregistrement du poids'
)]
class WeightMeasurementRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '75.50', description: 'Poids en kilogrammes')]
        public readonly string $valueKg,

        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', nullable: true, example: '175.00', description: 'Taille en centimètres')]
        public readonly ?string $heightCm
    ) {}
}
