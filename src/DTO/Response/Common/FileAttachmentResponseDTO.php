<?php

namespace App\DTO\Response\Common;

use App\Entity\Common\FileAttachment;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

#[OA\Schema(
    schema: 'FileAttachmentResponseDTO',
    title: 'FileAttachmentResponseDTO',
    description: 'Structure des données renvoyées pour une pièce jointe'
)]
class FileAttachmentResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '9a882211-12ee-4c55-8811-1a2233445566', description: 'Identifiant unique de la pièce jointe')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: 'resultats_analyse_sang.pdf', description: 'Nom original du fichier')]
        public readonly string $originalName,

        #[OA\Property(type: 'string', example: '68f02a11b24e4_resultats.pdf', description: 'Nom unique du fichier')]
        public readonly string $fileName,

        #[OA\Property(type: 'string', example: 'application/pdf', description: 'Type MIME')]
        public readonly string $mimeType,

        #[OA\Property(type: 'integer', example: 1048576, description: 'Taille en octets')]
        public readonly int $sizeBytes,

        #[OA\Property(type: 'string', format: 'uri', example: 'https://storage.diabcare.com/uploads/2026/08/68f02a11b24e4_resultats.pdf', description: 'URL d’accès')]
        public readonly string $url,

        #[OA\Property(type: 'string', example: 'MedicalRecord', description: 'Type d’entité liée')]
        public readonly string $entityType,

        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant de l’entité liée')]
        public readonly Uuid $entityId,

        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant de l’utilisateur')]
        public readonly string $uploadedById,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T12:00:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(FileAttachment $fileAttachment): self
    {
        return new self(
            id: (string) $fileAttachment->getId(),
            originalName: $fileAttachment->getOriginalName() ?? '',
            fileName: $fileAttachment->getFilename(),
            mimeType: $fileAttachment->getMimeType(),
            sizeBytes: $fileAttachment->getSizeBytes(),
            url: $fileAttachment->getUrl(),
            entityType: $fileAttachment->getEntityType(),
            entityId: $fileAttachment->getEntityId(),
            uploadedById: (string) $fileAttachment->getUploadedBy()?->getId(),
            createdAt: $fileAttachment->getCreatedAt(),
            updatedAt: $fileAttachment->getUpdatedAt()
        );
    }
}
