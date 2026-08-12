<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\MedicationIntake;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MedicationIntakeResponseDTO',
    title: 'MedicationIntakeResponseDTO',
    description: 'Structure de réponse pour une prise de médicament'
)]
class MedicationIntakeResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '55ee6677-8899-0011-2233-445566778899', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID de l’élément de prescription')]
        public readonly string $prescriptionItemId,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T08:00:00Z', description: 'Date et heure de la prise')]
        public readonly \DateTimeImmutable $takenAt,

        #[OA\Property(type: 'string', example: '1.00', description: 'Quantité prise')]
        public readonly string $quantityTaken,

        #[OA\Property(type: 'string', nullable: true, example: 'TAKEN', description: 'Statut de la prise')]
        public readonly ?string $status,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(MedicationIntake $intake): self
    {
        return new self(
            id: (string) $intake->getId(),
            prescriptionItemId: (string) $intake->getPrescriptionItem()?->getId(),
            takenAt: $intake->getTakenAt(),
            quantityTaken: $intake->getQuantityTaken(),
            status: $intake->getStatus()?->value,
            createdAt: $intake->getCreatedAt(),
            updatedAt: $intake->getUpdatedAt()
        );
    }
}
