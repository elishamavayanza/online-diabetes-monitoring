<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\PhysicalActivityMeasurement;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PhysicalActivityMeasurementResponseDTO',
    title: 'PhysicalActivityMeasurementResponseDTO',
    description: 'Structure de réponse pour une mesure d’activité physique'
)]
class PhysicalActivityMeasurementResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '55ee6677-8899-0011-2233-445566778899', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: 'Course à pied', description: 'Type d’activité')]
        public readonly string $activityType,

        #[OA\Property(type: 'integer', example: 30, description: 'Durée en minutes')]
        public readonly int $durationMinutes,

        #[OA\Property(type: 'string', nullable: true, example: '300.00', description: 'Calories brûlées')]
        public readonly ?string $caloriesBurned,

        #[OA\Property(type: 'string', nullable: true, example: '100', description: 'Fréquence cardiaque min')]
        public readonly ?string $minHeartRate,

        #[OA\Property(type: 'string', nullable: true, example: '160', description: 'Fréquence cardiaque max')]
        public readonly ?string $maxHeartRate,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(PhysicalActivityMeasurement $measurement): self
    {
        return new self(
            id: (string) $measurement->getId(),
            activityType: $measurement->getActivityType(),
            durationMinutes: $measurement->getDurationMinutes(),
            caloriesBurned: $measurement->getCaloriesBurned(),
            minHeartRate: $measurement->getMinHeartRate(),
            maxHeartRate: $measurement->getMaxHeartRate(),
            createdAt: $measurement->getCreatedAt(),
            updatedAt: $measurement->getUpdatedAt()
        );
    }
}
