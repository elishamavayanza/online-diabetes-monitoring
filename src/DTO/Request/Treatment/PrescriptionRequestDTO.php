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
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient')]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID du prescripteur')]
        public readonly string $prescriberId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID de l’organisation')]
        public readonly string $organizationId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T00:00:00Z', description: 'Date de début')]
        public readonly \DateTimeInterface $startDate,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: '2026-08-17T00:00:00Z', description: 'Date de fin')]
        public readonly ?\DateTimeInterface $endDate,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'ACTIVE', description: 'Statut')]
        public readonly mixed $status,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Notes cliniques...', description: 'Notes')]
        public readonly ?string $notes,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de validation')]
        public readonly ?\DateTimeImmutable $validatedAt,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: null, description: 'ID de l’utilisateur validateur')]
        public readonly ?string $validatedById
    ) {}
}
