<?php

namespace App\DTO\Response\Patient;

use App\Entity\Patient\Allergy;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'AllergyResponseDTO',
    description: 'Structure de réponse pour une allergie'
)]
class AllergyResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', example: 'Pénicilline', description: 'Nom de l’allergène')]
        public readonly string $name,

        #[OA\Property(type: 'string', nullable: true, example: 'SEVERE', description: 'Sévérité')]
        public readonly ?string $severity,

        #[OA\Property(type: 'string', nullable: true, example: 'Choc anaphylactique', description: 'Réaction')]
        public readonly ?string $reaction,

        #[OA\Property(type: 'string', nullable: true, example: 'Notes...', description: 'Notes')]
        public readonly ?string $notes,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date du diagnostic')]
        public readonly \DateTimeImmutable $diagnosedAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Allergy $allergy): self
    {
        return new self(
            id: (string) $allergy->getId(),
            patientId: (string) $allergy->getPatient()?->getId(),
            name: $allergy->getName(),
            severity: $allergy->getSeverity()?->value,
            reaction: $allergy->getReaction(),
            notes: $allergy->getNotes(),
            diagnosedAt: $allergy->getDiagnosedAt(),
            createdAt: $allergy->getCreatedAt(),
            updatedAt: $allergy->getUpdatedAt()
        );
    }
}
