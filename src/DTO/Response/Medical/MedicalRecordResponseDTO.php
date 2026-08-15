<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\MedicalRecord;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MedicalRecordResponseDTO',
    title: 'MedicalRecordResponseDTO',
    description: 'Structure de réponse pour un dossier médical'
)]
class MedicalRecordResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', format: 'uuid', example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID de l’organisation')]
        public readonly string $organizationId,

        #[OA\Property(type: 'string', example: 'OPEN', description: 'Statut')]
        public readonly string $status,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T08:00:00Z', description: 'Date d’ouverture')]
        public readonly \DateTimeImmutable $openedAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de fermeture')]
        public readonly ?\DateTimeImmutable $closedAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T09:00:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(MedicalRecord $record): self
    {
        return new self(
            id: (string) $record->getId(),
            patientId: (string) $record->getPatient()?->getId(),
            organizationId: (string) $record->getOrganization()?->getId(),
            status: $record->getStatus()?->value ?? '',
            openedAt: $record->getOpenedAt(),
            closedAt: $record->getClosedAt(),
            createdAt: $record->getCreatedAt(),
            updatedAt: $record->getUpdatedAt()
        );
    }
}
