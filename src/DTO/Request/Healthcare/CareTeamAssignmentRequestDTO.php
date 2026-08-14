<?php

namespace App\DTO\Request\Healthcare;

use App\Entity\Healthcare\CareTeamRole;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'CareTeamAssignmentRequestDTO',
    description: 'Structure des données requises pour la création d’une affectation d’équipe de soins'
)]
class CareTeamAssignmentRequestDTO
{
    public function __construct(
        #[Assert\Positive]
        #[OA\Property(type: 'integer', format: 'int64', example: 6, description: 'Identifiant du patient')]
        public readonly int $patientId,

        #[Assert\Positive]
        #[OA\Property(type: 'integer', format: 'int64', example: 14, description: 'Identifiant du professionnel de santé')]
        public readonly int $professionalId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', enum: ['PRIMARY_CLINICIAN', 'SPECIALIST', 'NUTRITIONIST'], example: 'PRIMARY_CLINICIAN', description: 'Rôle du professionnel')]
        public readonly CareTeamRole $role,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date', example: '2026-08-10', description: 'Date de début')]
        public readonly \DateTimeInterface $startDate,

        #[OA\Property(type: 'string', format: 'date', nullable: true, example: null, description: 'Date de fin')]
        public readonly ?\DateTimeInterface $endDate = null,

        #[Assert\NotNull]
        #[OA\Property(type: 'boolean', example: true, description: 'Statut actif')]
        public readonly bool $active = true
    ) {}
}
