<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\PrescriptionItem;

class PrescriptionItemResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $prescriptionId,
        public readonly string $medicationId,
        public readonly string $dosage,
        public readonly string $quantity,
        public readonly bool $morning,
        public readonly bool $noon,
        public readonly bool $evening,
        public readonly ?string $instructions,
        public readonly \DateTimeImmutable $createdAt,
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
