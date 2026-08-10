<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\OrganizationMembership;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'OrganizationMembershipResponseDTO',
    description: 'Structure des données renvoyées pour une adhésion à une organisation'
)]
class OrganizationMembershipResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '9f881245-33ee-4b11-9a21-4f88e1478c99', description: 'Identifiant unique de l’adhésion')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant de l’utilisateur')]
        public readonly string $userId,

        #[OA\Property(type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant de l’organisation')]
        public readonly string $organizationId,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '11bb22cc-33ee-4ff1-8811-9a8877665544', description: 'Identifiant de l’établissement')]
        public readonly ?string $facilityId,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '77cc88bb-11aa-4333-9988-123456789abc', description: 'Identifiant du département')]
        public readonly ?string $departmentId,

        #[OA\Property(type: 'string', format: 'date', example: '2026-08-10', description: 'Date de début')]
        public readonly \DateTimeInterface $startDate,

        #[OA\Property(type: 'string', format: 'date', nullable: true, example: null, description: 'Date de fin')]
        public readonly ?\DateTimeInterface $endDate,

        #[OA\Property(type: 'string', nullable: true, example: 'ACTIVE', description: 'Statut')]
        public readonly ?string $status,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(OrganizationMembership $membership): self
    {
        return new self(
            id: (string) $membership->getId(),
            userId: (string) $membership->getUser()?->getId(),
            organizationId: (string) $membership->getOrganization()?->getId(),
            facilityId: $membership->getFacility()?->getId() ? (string) $membership->getFacility()->getId() : null,
            departmentId: $membership->getDepartment()?->getId() ? (string) $membership->getDepartment()->getId() : null,
            startDate: $membership->getStartDate(),
            endDate: $membership->getEndDate(),
            status: $membership->getStatus()?->value,
            createdAt: $membership->getCreatedAt(),
            updatedAt: $membership->getUpdatedAt()
        );
    }
}
