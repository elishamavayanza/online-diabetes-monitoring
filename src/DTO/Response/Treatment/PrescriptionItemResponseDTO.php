<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\PrescriptionItem;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PrescriptionItemResponseDTO',
    title: 'PrescriptionItemResponseDTO',
    description: 'Structure de réponse pour un élément de prescription'
)]
class PrescriptionItemResponseDTO
{
    public function __construct(
        #[OA\Property(description: 'Identifiant unique', type: 'string', format: 'uuid', example: '77aa8899-0011-2233-4455-667788990011')]
        public readonly string $id,

        #[OA\Property(description: 'ID de la prescription', type: 'string', format: 'uuid', example: '99001122-3344-5566-7788-99aabbccddeev')]
        public readonly string $prescriptionId,

        #[OA\Property(description: 'ID du médicament', type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff')]
        public readonly string $medicationId,

        #[OA\Property(description: 'Nom du médicament', type: 'string', example: 'Paracétamol 500mg', nullable: true)]
        public readonly ?string $medicationName,

        #[OA\Property(description: 'Posologie', type: 'string', example: '1 comprimé')]
        public readonly string $dosage,

        #[OA\Property(description: 'Quantité', type: 'string', example: '1.00')]
        public readonly string $quantity,

        #[OA\Property(description: 'Matin', type: 'boolean', example: true)]
        public readonly bool $morning,

        #[OA\Property(description: 'Midi', type: 'boolean', example: false)]
        public readonly bool $noon,

        #[OA\Property(description: 'Soir', type: 'boolean', example: true)]
        public readonly bool $evening,

        #[OA\Property(description: 'Instructions', type: 'string', example: 'Instructions...', nullable: true)]
        public readonly ?string $instructions,

        #[OA\Property(description: 'Date de création', type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(description: 'Date de mise à jour', type: 'string', format: 'date-time', example: null, nullable: true)]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(PrescriptionItem $item): self
    {
        return new self(
            id: (string) $item->getId(),
            prescriptionId: (string) $item->getPrescription()?->getId(),
            medicationId: (string) $item->getMedication()?->getId(),
            medicationName: $item->getMedication()?->getName(),
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
