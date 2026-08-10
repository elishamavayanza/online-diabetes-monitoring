<?php

namespace App\DTO\Request\Medical;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'MedicalRecordRequestDTO',
    description: 'Structure de requête pour la création d’un dossier médical'
)]
class MedicalRecordRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'Identifiant du patient')]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'Identifiant de l’organisation')]
        public readonly string $organizationId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'ACTIVE', description: 'Statut du dossier')]
        public readonly mixed $status,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T08:00:00Z', description: 'Date d’ouverture')]
        public readonly \DateTimeImmutable $openedAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de fermeture')]
        public readonly ?\DateTimeImmutable $closedAt
    ) {}
}
