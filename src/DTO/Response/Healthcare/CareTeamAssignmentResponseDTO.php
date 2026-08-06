<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\CareTeamAssignment;

class CareTeamAssignmentResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $patientId,
        public readonly string $professionalId,
        public readonly string $organizationId,
        public readonly ?string $role,
        public readonly \DateTimeInterface $startDate,
        public readonly ?\DateTimeInterface $endDate,
        public readonly bool $active,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(CareTeamAssignment $assignment): self
    {
        return new self(
            id: (string) $assignment->getId(),
            patientId: (string) $assignment->getPatient()?->getId(),
            professionalId: (string) $assignment->getProfessional()?->getId(),
            organizationId: (string) $assignment->getOrganization()?->getId(),
            role: $assignment->getRole()?->value,
            startDate: $assignment->getStartDate(),
            endDate: $assignment->getEndDate(),
            active: $assignment->isActive(),
            createdAt: $assignment->getCreatedAt(),
            updatedAt: $assignment->getUpdatedAt()
        );
    }
}
