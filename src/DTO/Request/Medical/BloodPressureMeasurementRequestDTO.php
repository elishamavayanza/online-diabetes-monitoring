<?php

namespace App\DTO\Request\Medical;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'BloodPressureMeasurementRequestDTO',
    description: 'Structure de requête pour l’enregistrement d’une mesure de tension artérielle'
)]
class BloodPressureMeasurementRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '120', description: 'Pression systolique')]
        public readonly string $systolic,

        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '80', description: 'Pression diastolique')]
        public readonly string $diastolic,

        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', nullable: true, example: '72', description: 'Pouls / fréquence cardiaque')]
        public readonly ?string $pulse
    ) {}
}
