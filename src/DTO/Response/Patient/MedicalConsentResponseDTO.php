<?php

namespace App\DTO\Response\Patient;

use App\Entity\Patient\MedicalConsent;

class MedicalConsentResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $patientId,
        public readonly ?string $organizationId,
        public readonly ?string $consentType,
        public readonly \DateTimeImmutable $grantedAt,
        public readonly ?\DateTimeImmutable $revokedAt,
        public readonly ?string $documentUrl,
        public readonly \DateTimeImmutable $createdAt,
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
