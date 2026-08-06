<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\MedicalRecord;

class MedicalRecordResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $patientId,
        public readonly string $organizationId,
        public readonly string $status,
        public readonly \DateTimeImmutable $openedAt,
        public readonly ?\DateTimeImmutable $closedAt,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(MedicalRecord $record): self
    {
        return new self(
            id: (string) $record->getId(),
            patientId: (string) $record->getPatient()?->getId(),
            organizationId: (string) $record->getOrganization()?->getId(),
            status: $record->getStatus()?->value ?? '',
            openedAt: $record->getOpenedAt(),
            closedAt: $record->getClosedAt(),
            createdAt: $record->getCreatedAt(),
            updatedAt: $record->getUpdatedAt()
        );
    }
}
