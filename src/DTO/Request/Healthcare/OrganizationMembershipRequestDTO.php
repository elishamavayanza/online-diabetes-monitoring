<?php

namespace App\DTO\Request\Healthcare;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'OrganizationMembershipRequestDTO',
    description: 'Structure des données requises pour la création d’une adhésion à une organisation'
)]
class OrganizationMembershipRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant de l’utilisateur')]
        public readonly string $userId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant de l’organisation')]
        public readonly string $organizationId,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '11bb22cc-33ee-4ff1-8811-9a8877665544', description: 'Identifiant de l’établissement')]
        public readonly ?string $facilityId,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '77cc88bb-11aa-4333-9988-123456789abc', description: 'Identifiant du département')]
        public readonly ?string $departmentId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date', example: '2026-08-10', description: 'Date de début')]
        public readonly \DateTimeInterface $startDate,

        #[OA\Property(type: 'string', format: 'date', nullable: true, example: null, description: 'Date de fin')]
        public readonly ?\DateTimeInterface $endDate,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'ACTIVE', description: 'Statut de l’adhésion')]
        public readonly mixed $status
    ) {}
}
