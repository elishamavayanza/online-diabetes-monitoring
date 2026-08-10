<?php

namespace App\DTO\Response\Patient;

use App\Entity\Patient\MedicalConsent;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'MedicalConsentResponseDTO',
    description: 'Structure de réponse pour un consentement médical'
)]
class MedicalConsentResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '99001122-3344-5566-7788-99aabbccddeev', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID de l’organisation')]
        public readonly ?string $organizationId,

        #[OA\Property(type: 'string', nullable: true, example: 'DATA_SHARING', description: 'Type de consentement')]
        public readonly ?string $consentType,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date d’octroi')]
        public readonly \DateTimeImmutable $grantedAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de révocation')]
        public readonly ?\DateTimeImmutable $revokedAt,

        #[OA\Property(type: 'string', format: 'uri', nullable: true, example: 'https://example.com/doc.pdf', description: 'URL du document')]
        public readonly ?string $documentUrl,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
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
