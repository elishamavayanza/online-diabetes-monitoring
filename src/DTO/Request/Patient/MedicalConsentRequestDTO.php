<?php

namespace App\DTO\Request\Patient;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'MedicalConsentRequestDTO',
    description: 'Structure de requête pour la création ou modification d’un consentement médical avec fichier joint'
)]
class MedicalConsentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(description: 'ID du patient', type: 'integer', example: 123)]
        public readonly string $patientId,

        #[OA\Property(description: 'ID de l’organisation', type: 'integer', example: 45, nullable: true)]
        public readonly ?string $organizationId,

        #[Assert\NotBlank]
        #[OA\Property(description: 'Type de consentement', type: 'string', example: 'DATA_SHARING')]
        public readonly mixed $consentType,

        #[Assert\NotBlank]
        #[OA\Property(description: 'Date d’octroi', type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z')]
        public readonly \DateTimeImmutable $grantedAt,

        #[OA\Property(description: 'Date de révocation', type: 'string', format: 'date-time', example: null, nullable: true)]
        public readonly ?\DateTimeImmutable $revokedAt,

        #[Assert\File(
            maxSize: '10M',
            mimeTypes: [
                'application/pdf',
                'application/x-pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png',
                'image/webp'
            ],
            mimeTypesMessage: 'Veuillez uploader un fichier valide (PDF, Word ou Image JPEG/PNG/WebP).'
        )]
        #[OA\Property(description: 'Fichier document (PDF, Word ou Image)', type: 'string', format: 'binary', nullable: true)]
        public readonly ?UploadedFile $documentFile = null
    ) {}
}
