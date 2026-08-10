<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\PrescriptionItem;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'PrescriptionItemResponseDTO',
    description: 'Structure de réponse pour un élément de prescription'
)]
class PrescriptionItemResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '77aa8899-0011-2233-4455-667788990011', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '99001122-3344-5566-7788-99aabbccddeev', description: 'ID de la prescription')]
        public readonly string $prescriptionId,

        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID du médicament')]
        public readonly string $medicationId,

        #[OA\Property(type: 'string', example: '1 comprimé', description: 'Posologie')]
        public readonly string $dosage,

        #[OA\Property(type: 'string', example: '1.00', description: 'Quantité')]
        public readonly string $quantity,

        #[OA\Property(type: 'boolean', example: true, description: 'Matin')]
        public readonly bool $morning,

        #[OA\Property(type: 'boolean', example: false, description: 'Midi')]
        public readonly bool $noon,

        #[OA\Property(type: 'boolean', example: true, description: 'Soir')]
        public readonly bool $evening,

        #[OA\Property(type: 'string', nullable: true, example: 'Instructions...', description: 'Instructions')]
        public readonly ?string $instructions,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(PrescriptionItem $item): self
    {
        return new self(
            id: (string) $item->getId(),
            prescriptionId: (string) $item->getPrescription()?->getId(),
            medicationId: (string) $item->getMedication()?->getId(),
            dosage: $item->getDosage(),
            quantity: $item->getQuantity(),
            morning: $item->isMorning(),
            noon: $item->isNoon(),
            evening: $item->isEvening(),
            instructions: $item->getInstructions(),
            createdAt: $item->getCreatedAt(),
            updatedAt: $item->getUpdatedAt()
        );
    }
}
