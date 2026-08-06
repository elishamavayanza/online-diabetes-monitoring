<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\Department;

class DepartmentResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $facilityId,
        public readonly string $name,
        public readonly ?string $specialty,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Department $department): self
    {
        return new self(
            id: (string) $department->getId(),
            facilityId: (string) $department->getFacility()?->getId(),
            name: $department->getName(),
            specialty: $department->getSpecialty(),
            createdAt: $department->getCreatedAt(),
            updatedAt: $department->getUpdatedAt()
        );
    }
}
