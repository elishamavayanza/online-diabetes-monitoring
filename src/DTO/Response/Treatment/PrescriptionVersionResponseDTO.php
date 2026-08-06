<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\PrescriptionVersion;

class PrescriptionVersionResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $prescriptionId,
        public readonly int $versionNumber,
        public readonly ?string $changesSummary,
        public readonly array $data,
        public readonly string $modifiedById,
        public readonly \DateTimeImmutable $modifiedAt,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(PrescriptionVersion $version): self
    {
        return new self(
            id: (string) $version->getId(),
            prescriptionId: (string) $version->getPrescription()?->getId(),
            versionNumber: $version->getVersionNumber(),
            changesSummary: $version->getChangesSummary(),
            data: $version->getData(),
            modifiedById: (string) $version->getModifiedBy()?->getId(),
            modifiedAt: $version->getModifiedAt(),
            createdAt: $version->getCreatedAt(),
            updatedAt: $version->getUpdatedAt()
        );
    }
}
