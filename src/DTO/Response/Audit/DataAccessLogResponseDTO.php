<?php

namespace App\DTO\Response\Audit;

use App\Entity\Audit\DataAccessLog;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'DataAccessLogResponseDTO',
    description: 'Structure des données renvoyées pour une entrée de journal d’accès'
)]
class DataAccessLogResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '9a882211-12ee-4c55-8811-1a2233445566', description: 'Identifiant unique du log')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant de l’utilisateur')]
        public readonly string $accessedById,

        #[OA\Property(type: 'string', format: 'uuid', example: '4a613328-98e3-4d64-8898-0c06a3861c8f', description: 'Identifiant du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', example: 'MedicalRecord', description: 'Type de ressource')]
        public readonly string $resourceType,

        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant de la ressource')]
        public readonly string $resourceId,

        #[OA\Property(type: 'string', nullable: true, example: 'Consultation d’urgence dans le cadre d’une hospitalisation', description: 'Motif')]
        public readonly ?string $reason,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:15:00Z', description: 'Date de l’accès')]
        public readonly \DateTimeImmutable $accessedAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:15:05Z', description: 'Date de création de l’enregistrement')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(DataAccessLog $log): self
    {
        return new self(
            id: (string) $log->getId(),
            accessedById: (string) $log->getAccessedBy()?->getId(),
            patientId: (string) $log->getPatient()?->getId(),
            resourceType: $log->getResourceType(),
            resourceId: $log->getResourceId(),
            reason: $log->getReason(),
            accessedAt: $log->getAccessedAt(),
            createdAt: $log->getCreatedAt(),
            updatedAt: $log->getUpdatedAt()
        );
    }
}
