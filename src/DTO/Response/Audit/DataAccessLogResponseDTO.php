<?php

namespace App\DTO\Response\Audit;

use App\Entity\Audit\DataAccessLog;

class DataAccessLogResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $accessedById,
        public readonly string $patientId,
        public readonly string $resourceType,
        public readonly string $resourceId,
        public readonly ?string $reason,
        public readonly \DateTimeImmutable $accessedAt,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(DataAccessLog $log): self
    {
        return new self(
            id: (string) $log->getId(),
            accessedById: (string) $log->getAccessedBy()?->getId(),
            patientId: (string) $log->getPatient()?->getId(),
            resourceType: $log->getResourceType(),
            resourceId: $log->getResourceId(),
            reason: $log->getReason(),
            accessedAt: $log->getAccessedAt(),
            createdAt: $log->getCreatedAt(),
            updatedAt: $log->getUpdatedAt()
        );
    }
}
