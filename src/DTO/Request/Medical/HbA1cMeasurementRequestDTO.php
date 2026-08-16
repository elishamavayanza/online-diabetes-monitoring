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
        #[OA\Property(description: 'Valeur de l’HbA1c en pourcentage', type: 'string', example: '6.5')]
        public readonly string $valuePercent,

        #[OA\Property(description: 'Date et heure de la mesure (optionnel, prend l’heure actuelle si vide)', type: 'string', format: 'date-time', example: '2026-08-16T10:00:00Z')]
        public readonly ?string $measuredAt = null
    ) {}
}
