<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\WeightMeasurement;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'WeightMeasurementResponseDTO',
    title: 'WeightMeasurementResponseDTO',
    description: 'Structure de réponse pour une mesure de poids'
)]
class WeightMeasurementResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '99001122-3344-5566-7788-99aabbccddeev', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: '75.50', description: 'Poids en kg')]
        public readonly string $valueKg,

        #[OA\Property(type: 'string', nullable: true, example: '175.00', description: 'Taille en cm')]
        public readonly ?string $heightCm,

        #[OA\Property(type: 'string', nullable: true, example: '24.65', description: 'Indice de Masse Corporelle (IMC)')]
        public readonly ?string $bmi,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(WeightMeasurement $measurement): self
    {
        return new self(
            id: (string) $measurement->getId(),
            valueKg: $measurement->getValueKg(),
            heightCm: $measurement->getHeightCm(),
            bmi: $measurement->getBmi(),
            createdAt: $measurement->getCreatedAt(),
            updatedAt: $measurement->getUpdatedAt()
        );
    }
}
