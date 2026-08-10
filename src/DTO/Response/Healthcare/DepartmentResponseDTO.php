<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\Department;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'DepartmentResponseDTO',
    description: 'Structure des données renvoyées pour un département médical'
)]
class DepartmentResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '77cc88bb-11aa-4333-9988-123456789abc', description: 'Identifiant unique du département')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '11bb22cc-33ee-4ff1-8811-9a8877665544', description: 'Identifiant de l’établissement')]
        public readonly string $facilityId,

        #[OA\Property(type: 'string', example: 'Cardiologie', description: 'Nom')]
        public readonly string $name,

        #[OA\Property(type: 'string', nullable: true, example: 'Cardiologie interventionnelle', description: 'Spécialité')]
        public readonly ?string $specialty,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
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
