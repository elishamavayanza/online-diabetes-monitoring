<?php

namespace App\DTO\Request\Medical;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'PhysicalActivityMeasurementRequestDTO',
    description: 'Structure de requête pour l’enregistrement d’une activité physique'
)]
class PhysicalActivityMeasurementRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, example: 'Course à pied', description: 'Type d’activité physique')]
        public readonly string $activityType,

        #[Assert\NotBlank]
        #[Assert\Positive]
        #[OA\Property(type: 'integer', example: 30, description: 'Durée en minutes')]
        public readonly int $durationMinutes,

        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', nullable: true, example: '300.00', description: 'Calories brûlées')]
        public readonly ?string $caloriesBurned,

        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', nullable: true, example: '100', description: 'Fréquence cardiaque min')]
        public readonly ?string $minHeartRate,

        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', nullable: true, example: '160', description: 'Fréquence cardiaque max')]
        public readonly ?string $maxHeartRate
    ) {}
}
