<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\MedicationIntake;

class MedicationIntakeResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $prescriptionItemId,
        public readonly \DateTimeImmutable $takenAt,
        public readonly string $quantityTaken,
        public readonly ?string $status,
        public readonly \DateTimeImmutable $createdAt,
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
