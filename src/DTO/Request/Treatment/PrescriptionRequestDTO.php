<?php

namespace App\DTO\Request\Treatment;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'PrescriptionRequestDTO',
    description: 'Structure de requête pour la création d’une prescription'
)]
class PrescriptionRequestDTO
{
    #[Assert\NotBlank]
    #[OA\Property(description: 'ID du patient', type: 'string', example: '13')]
    public ?string $patientId = null;

    #[Assert\NotBlank]
    #[OA\Property(description: 'ID du prescripteur', type: 'string', example: '14')]
    public ?string $prescriberId = null;

    #[Assert\NotBlank]
    #[OA\Property(description: 'ID de l’organisation', type: 'string', example: '12')]
    public ?string $organizationId = null;

    #[Assert\NotNull]
    #[OA\Property(description: 'Date de début', type: 'string', format: 'date-time', example: '2026-08-10T00:00:00Z')]
    public ?\DateTimeImmutable $startDate = null;

    #[OA\Property(description: 'Date de fin', type: 'string', format: 'date-time', example: '2026-08-17T00:00:00Z', nullable: true)]
    public ?\DateTimeImmutable $endDate = null;

    #[Assert\NotBlank]
    #[OA\Property(description: 'Statut', type: 'string', example: 'ACTIVE')]
    public ?string $status = null;

    #[Assert\Length(max: 5000)]
    #[OA\Property(description: 'Notes', type: 'string', example: 'Notes cliniques...', nullable: true, maxLength: 5000)]
    public ?string $notes = null;

    #[OA\Property(description: 'Date de validation', type: 'string', format: 'date-time', example: null, nullable: true)]
    public ?\DateTimeImmutable $validatedAt = null;

    #[OA\Property(description: 'ID de l’utilisateur validateur', type: 'string', example: null, nullable: true)]
    public ?string $validatedById = null;
}
