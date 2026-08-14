<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\CareTeamAssignment;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CareTeamAssignmentResponseDTO',
    title: 'CareTeamAssignmentResponseDTO',
    description: 'Structure des données renvoyées pour une affectation d’équipe de soins'
)]
class CareTeamAssignmentResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', format: 'int64', example: 21, description: 'Identifiant unique de l’affectation')]
        public readonly string $id,

        #[OA\Property(type: 'integer', format: 'int64', example: 6, description: 'Identifiant du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'integer', format: 'int64', example: 14, description: 'Identifiant du professionnel de santé')]
        public readonly string $professionalId,

        #[OA\Property(type: 'integer', format: 'int64', example: 2, description: 'Identifiant de l’organisation')]
        public readonly string $organizationId,

        #[OA\Property(type: 'string', nullable: true, example: 'ATTENDING_PHYSICIAN', description: 'Rôle')]
        public readonly ?string $role,

        #[OA\Property(type: 'string', format: 'date', example: '2026-08-10', description: 'Date de début')]
        public readonly \DateTimeInterface $startDate,

        #[OA\Property(type: 'string', format: 'date', nullable: true, example: null, description: 'Date de fin')]
        public readonly ?\DateTimeInterface $endDate,

        #[OA\Property(type: 'boolean', example: true, description: 'Statut actif')]
        public readonly bool $active,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
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
