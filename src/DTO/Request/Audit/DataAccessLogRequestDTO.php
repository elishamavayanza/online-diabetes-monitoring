<?php

namespace App\DTO\Request\Audit;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'DataAccessLogRequestDTO',
    description: 'Structure des données requises pour la création d’une entrée de journal d’accès'
)]
class DataAccessLogRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant de l’utilisateur accédant aux données')]
        public readonly string $accessedById,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '4a613328-98e3-4d64-8898-0c06a3861c8f', description: 'Identifiant du patient concerné')]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'MedicalRecord', description: 'Type de ressource accédée')]
        public readonly string $resourceType,

        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant unique de la ressource')]
        public readonly string $resourceId,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Consultation d’urgence dans le cadre d’une hospitalisation', description: 'Motif de l’accès')]
        public readonly ?string $reason,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:15:00Z', description: 'Horodatage de l’accès')]
        public readonly \DateTimeImmutable $accessedAt
    ) {}
}
