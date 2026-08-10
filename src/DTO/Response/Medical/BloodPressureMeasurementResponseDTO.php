<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\BloodPressureMeasurement;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'BloodPressureMeasurementResponseDTO',
    description: 'Structure de réponse pour une mesure de tension artérielle'
)]
class BloodPressureMeasurementResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '88bb7766-5544-3322-1100-ffaabbccddeey', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: '120', description: 'Systolique')]
        public readonly string $systolic,

        #[OA\Property(type: 'string', example: '80', description: 'Diastolique')]
        public readonly string $diastolic,

        #[OA\Property(type: 'string', nullable: true, example: '72', description: 'Pouls')]
        public readonly ?string $pulse,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(BloodPressureMeasurement $measurement): self
    {
        return new self(
            id: (string) $measurement->getId(),
            systolic: $measurement->getSystolic(),
            diastolic: $measurement->getDiastolic(),
            pulse: $measurement->getPulse(),
            createdAt: $measurement->getCreatedAt(),
            updatedAt: $measurement->getUpdatedAt()
        );
    }
}
