<?php

namespace App\DTO\Request\Healthcare;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'CareTeamAssignmentRequestDTO',
    description: 'Structure des données requises pour la création d’une affectation d’équipe de soins'
)]
class CareTeamAssignmentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant du patient')]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant du professionnel de santé')]
        public readonly string $professionalId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant de l’organisation')]
        public readonly string $organizationId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'ATTENDING_PHYSICIAN', description: 'Rôle du professionnel')]
        public readonly mixed $role,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date', example: '2026-08-10', description: 'Date de début')]
        public readonly \DateTimeInterface $startDate,

        #[OA\Property(type: 'string', format: 'date', nullable: true, example: null, description: 'Date de fin')]
        public readonly ?\DateTimeInterface $endDate,

        #[Assert\NotNull]
        #[OA\Property(type: 'boolean', example: true, description: 'Statut actif')]
        public readonly bool $active
    ) {}
}
