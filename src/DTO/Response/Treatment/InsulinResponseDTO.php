<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\Insulin;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InsulinResponseDTO',
    title: 'InsulinResponseDTO',
    description: 'Structure de réponse pour une insuline'
)]
class InsulinResponseDTO
{
    public function __construct(
        #[OA\Property(description: 'Identifiant unique', type: 'string', format: 'uuid', example: '66bb1245-12f4-4b53-8811-7a6543210999')]
        public readonly string $id,

        #[OA\Property(description: 'ID du médicament associé', type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff')]
        public readonly string $medicationId,

        #[OA\Property(description: 'Nom du médicament associé', type: 'string', example: 'Lantus 100 UI/ml', nullable: true)]
        public readonly ?string $medicationName,

        #[OA\Property(description: 'Type d’insuline', type: 'string', example: 'LONG_ACTING', nullable: true)]
        public readonly ?string $insulinType,

        #[OA\Property(description: 'Concentration de l’insuline', type: 'string', example: 'U-100')]
        public readonly string $concentration,

        #[OA\Property(description: 'Date de création', type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(description: 'Date de mise à jour', type: 'string', format: 'date-time', example: null, nullable: true)]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Insulin $insulin): self
    {
        return new self(
            id: (string) $insulin->getId(),
            medicationId: (string) $insulin->getMedication()?->getId(),
            medicationName: $insulin->getMedication()?->getName(),
            insulinType: $insulin->getInsulinType()?->value,
            concentration: $insulin->getConcentration(),
            createdAt: $insulin->getCreatedAt(),
            updatedAt: $insulin->getUpdatedAt()
        );
    }
}