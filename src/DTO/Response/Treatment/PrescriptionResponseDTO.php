<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\Prescription;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'PrescriptionResponseDTO',
    description: 'Structure de réponse pour une prescription'
)]
class PrescriptionResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '99001122-3344-5566-7788-99aabbccddeev', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID du prescripteur')]
        public readonly string $prescriberId,

        #[OA\Property(type: 'string', format: 'uuid', example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID de l’organisation')]
        public readonly string $organizationId,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T00:00:00Z', description: 'Date de début')]
        public readonly \DateTimeInterface $startDate,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: '2026-08-17T00:00:00Z', description: 'Date de fin')]
        public readonly ?\DateTimeInterface $endDate,

        #[OA\Property(type: 'string', nullable: true, example: 'ACTIVE', description: 'Statut')]
        public readonly ?string $status,

        #[OA\Property(type: 'string', nullable: true, example: 'Notes...', description: 'Notes')]
        public readonly ?string $notes,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de validation')]
        public readonly ?\DateTimeImmutable $validatedAt,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: null, description: 'ID de l’utilisateur validateur')]
        public readonly ?string $validatedById,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Prescription $prescription): self
    {
        return new self(
            id: (string) $prescription->getId(),
            patientId: (string) $prescription->getPatient()?->getId(),
            prescriberId: (string) $prescription->getPrescriber()?->getId(),
            organizationId: (string) $prescription->getOrganization()?->getId(),
            startDate: $prescription->getStartDate(),
            endDate: $prescription->getEndDate(),
            status: $prescription->getStatus()?->value,
            notes: $prescription->getNotes(),
            validatedAt: $prescription->getValidatedAt(),
            validatedById: $prescription->getValidatedBy()?->getId() ? (string) $prescription->getValidatedBy()->getId() : null,
            createdAt: $prescription->getCreatedAt(),
            updatedAt: $prescription->getUpdatedAt()
        );
    }
}
