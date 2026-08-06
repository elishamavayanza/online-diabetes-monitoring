<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\Prescription;

class PrescriptionResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $patientId,
        public readonly string $prescriberId,
        public readonly string $organizationId,
        public readonly \DateTimeInterface $startDate,
        public readonly ?\DateTimeInterface $endDate,
        public readonly ?string $status,
        public readonly ?string $notes,
        public readonly ?\DateTimeImmutable $validatedAt,
        public readonly ?string $validatedById,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Prescription $prescription): self
    {
        return new self(
            id: (string) $prescription->getId(),
            patientId: (string) $prescription->getPatient()?->getId(),
            prescriberId: (string) $prescription->getPrescriber()?->getId(),
            organizationId: (string) $prescription->getOrganization()?->getId(),
            startDate: $prescription->getStartDate(),
            endDate: $prescription->getEndDate(),
            status: $prescription->getStatus()?->value,
            notes: $prescription->getNotes(),
            validatedAt: $prescription->getValidatedAt(),
            validatedById: $prescription->getValidatedBy()?->getId() ? (string) $prescription->getValidatedBy()->getId() : null,
            createdAt: $prescription->getCreatedAt(),
            updatedAt: $prescription->getUpdatedAt()
        );
    }
}
