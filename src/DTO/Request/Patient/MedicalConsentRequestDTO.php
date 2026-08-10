<?php

namespace App\DTO\Request\Patient;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'MedicalConsentRequestDTO',
    description: 'Structure de requête pour la création d’un consentement médical'
)]
class MedicalConsentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID de l’organisation')]
        public readonly ?string $organizationId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'DATA_SHARING', description: 'Type de consentement')]
        public readonly mixed $consentType,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date d’octroi')]
        public readonly \DateTimeImmutable $grantedAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de révocation')]
        public readonly ?\DateTimeImmutable $revokedAt,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://example.com/doc.pdf', description: 'URL du document')]
        public readonly ?string $documentUrl
    ) {}
}
