<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\PrescriptionVersion;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'PrescriptionVersionResponseDTO',
    description: 'Structure de réponse pour une version de prescription'
)]
class PrescriptionVersionResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '44bb5566-7788-9900-1122-334455667788', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '99001122-3344-5566-7788-99aabbccddeev', description: 'ID de la prescription')]
        public readonly string $prescriptionId,

        #[OA\Property(type: 'integer', example: 2, description: 'Numéro de version')]
        public readonly int $versionNumber,

        #[OA\Property(type: 'string', nullable: true, example: 'Mise à jour posologie', description: 'Résumé des modifications')]
        public readonly ?string $changesSummary,

        #[OA\Property(type: 'object', example: ['key' => 'value'], description: 'Données de la version')]
        public readonly array $data,

        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID de l’auteur')]
        public readonly string $modifiedById,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:00:00Z', description: 'Date de modification')]
        public readonly \DateTimeImmutable $modifiedAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(PrescriptionVersion $version): self
    {
        return new self(
            id: (string) $version->getId(),
            prescriptionId: (string) $version->getPrescription()?->getId(),
            versionNumber: $version->getVersionNumber(),
            changesSummary: $version->getChangesSummary(),
            data: $version->getData(),
            modifiedById: (string) $version->getModifiedBy()?->getId(),
            modifiedAt: $version->getModifiedAt(),
            createdAt: $version->getCreatedAt(),
            updatedAt: $version->getUpdatedAt()
        );
    }
}
