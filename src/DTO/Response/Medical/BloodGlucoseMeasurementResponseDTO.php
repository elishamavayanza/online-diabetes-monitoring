<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\BloodGlucoseMeasurement;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'BloodGlucoseMeasurementResponseDTO',
    description: 'Structure de réponse pour une mesure de glycémie'
)]
class BloodGlucoseMeasurementResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '55aa4433-2211-4453-9988-776655443322', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: '1.26', description: 'Valeur de la glycémie')]
        public readonly string $value,

        #[OA\Property(type: 'string', nullable: true, example: 'g/L', description: 'Unité')]
        public readonly ?string $unit,

        #[OA\Property(type: 'string', nullable: true, example: 'FASTING', description: 'Contexte')]
        public readonly ?string $context,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(BloodGlucoseMeasurement $measurement): self
    {
        return new self(
            id: (string) $measurement->getId(),
            value: $measurement->getValue(),
            unit: $measurement->getUnit()?->value,
            context: $measurement->getContext()?->value,
            createdAt: $measurement->getCreatedAt(),
            updatedAt: $measurement->getUpdatedAt()
        );
    }
}
