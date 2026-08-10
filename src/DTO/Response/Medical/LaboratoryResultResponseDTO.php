<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\LaboratoryResult;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'LaboratoryResultResponseDTO',
    description: 'Structure de réponse pour un résultat de laboratoire'
)]
class LaboratoryResultResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '44cc3322-1100-9988-7766-554433221100', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: 'Bilan lipidique complet', description: 'Nom de l’examen')]
        public readonly string $testName,

        #[OA\Property(type: 'string', format: 'uri', nullable: true, example: 'https://storage.diabcare.com/labs/result-123.pdf', description: 'URL du fichier')]
        public readonly ?string $fileUrl,

        #[OA\Property(type: 'string', nullable: true, example: 'Laboratoire Central Goma', description: 'Nom du laboratoire')]
        public readonly ?string $labName,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(LaboratoryResult $result): self
    {
        return new self(
            id: (string) $result->getId(),
            testName: $result->getTestName(),
            fileUrl: $result->getFileUrl(),
            labName: $result->getLabName(),
            createdAt: $result->getCreatedAt(),
            updatedAt: $result->getUpdatedAt()
        );
    }
}
