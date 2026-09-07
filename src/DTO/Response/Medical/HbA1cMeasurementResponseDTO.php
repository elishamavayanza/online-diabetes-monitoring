<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\HbA1cMeasurement;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'HbA1cMeasurementResponseDTO',
    title: 'HbA1cMeasurementResponseDTO',
    description: 'Structure de réponse pour une mesure d’HbA1c'
)]
class HbA1cMeasurementResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '77bb6655-4433-2211-0099-887766554433', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: '6.5', description: 'Valeur en pourcentage')]
        public readonly string $valuePercent,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID de l\'auteur')]
        public readonly ?string $createdById,

        #[OA\Property(type: 'string', nullable: true, example: 'Dr. Dupont', description: 'Nom de l\'auteur')]
        public readonly ?string $createdByName
    ) {}

    public static function fromEntity(HbA1cMeasurement $measurement): self
    {
        return new self(
            id: (string) $measurement->getId(),
            valuePercent: $measurement->getValuePercent(),
            createdAt: $measurement->getCreatedAt(),
            updatedAt: $measurement->getUpdatedAt(),
            createdById: (string) $measurement->getIssuer()?->getId(),
            createdByName: $measurement->getIssuer()?->getFullName()
        );
    }
}
