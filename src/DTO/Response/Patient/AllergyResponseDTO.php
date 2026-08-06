<?php

namespace App\DTO\Response\Patient;

use App\Entity\Patient\Allergy;

class AllergyResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $patientId,
        public readonly string $name,
        public readonly ?string $severity,
        public readonly ?string $reaction,
        public readonly ?string $notes,
        public readonly \DateTimeImmutable $diagnosedAt,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Allergy $allergy): self
    {
        return new self(
            id: (string) $allergy->getId(),
            patientId: (string) $allergy->getPatient()?->getId(),
            name: $allergy->getName(),
            severity: $allergy->getSeverity()?->value,
            reaction: $allergy->getReaction(),
            notes: $allergy->getNotes(),
            diagnosedAt: $allergy->getDiagnosedAt(),
            createdAt: $allergy->getCreatedAt(),
            updatedAt: $allergy->getUpdatedAt()
        );
    }
}
