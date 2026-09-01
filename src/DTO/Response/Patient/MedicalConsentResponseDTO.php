<?php

namespace App\DTO\Response\Patient;

use App\Entity\Patient\MedicalConsent;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MedicalConsentResponseDTO',
    title: 'MedicalConsentResponseDTO',
    description: 'Structure de réponse pour un consentement médical'
)]
class MedicalConsentResponseDTO
{
    public function __construct(
        #[OA\Property(description: 'Identifiant unique', type: 'integer', example: 1)]
        public readonly string $id,

        #[OA\Property(description: 'ID du patient', type: 'integer', example: 123)]
        public readonly string $patientId,

        #[OA\Property(description: 'ID de l’organisation', type: 'integer', example: 45, nullable: true)]
        public readonly ?string $organizationId,

        #[OA\Property(description: 'Type de consentement', type: 'string', example: 'DATA_SHARING', nullable: true)]
        public readonly ?string $consentType,

        #[OA\Property(description: 'Date d’octroi', type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z')]
        public readonly \DateTimeImmutable $grantedAt,

        #[OA\Property(description: 'Date de révocation', type: 'string', format: 'date-time', example: null, nullable: true)]
        public readonly ?\DateTimeImmutable $revokedAt,

        #[OA\Property(description: 'Nom/Chemin du fichier document', type: 'string', example: 'nom-du-fichier-unique.pdf', nullable: true)]
        public readonly ?string $documentUrl,

        #[OA\Property(description: 'Date de création', type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(description: 'Date de mise à jour', type: 'string', format: 'date-time', example: null, nullable: true)]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(MedicalConsent $consent): self
    {
        return new self(
            id: (string) $consent->getId(),
            patientId: (string) $consent->getPatient()?->getId(),
            organizationId: $consent->getOrganization()?->getId() ? (string) $consent->getOrganization()->getId() : null,
            consentType: $consent->getConsentType()?->value,
            grantedAt: $consent->getGrantedAt(),
            revokedAt: $consent->getRevokedAt(),
            documentUrl: $consent->getDocumentUrl(),
            createdAt: $consent->getCreatedAt(),
            updatedAt: $consent->getUpdatedAt()
        );
    }
}
