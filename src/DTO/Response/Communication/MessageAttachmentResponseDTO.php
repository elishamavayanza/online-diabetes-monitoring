<?php

namespace App\DTO\Response\Communication;

use App\Entity\Communication\MessageAttachment;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MessageAttachmentResponseDTO',
    title: 'MessageAttachmentResponseDTO',
    description: 'Structure des données renvoyées pour une pièce jointe de message'
)]
class MessageAttachmentResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '11bb22cc-33ee-4ff1-8811-9a8877665544', description: 'Identifiant unique de la pièce jointe')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '9f881245-33ee-4b11-9a21-4f88e1478c99', description: 'Identifiant du message')]
        public readonly string $messageId,

        #[OA\Property(type: 'string', format: 'uri', example: 'https://storage.diabcare.com/messages/bilan_sanguin.pdf', description: 'URL du fichier')]
        public readonly string $fileUrl,

        #[OA\Property(type: 'string', example: 'bilan_sanguin.pdf', description: 'Nom du fichier')]
        public readonly string $fileName,

        #[OA\Property(type: 'string', example: 'application/pdf', description: 'Type MIME')]
        public readonly string $mimeType,

        #[OA\Property(type: 'integer', example: 512000, description: 'Taille en octets')]
        public readonly int $sizeBytes,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(MessageAttachment $attachment): self
    {
        return new self(
            id: (string) $attachment->getId(),
            messageId: (string) $attachment->getMessage()?->getId(),
            fileUrl: $attachment->getFileUrl(),
            fileName: $attachment->getFileName(),
            mimeType: $attachment->getMimeType(),
            sizeBytes: $attachment->getSizeBytes(),
            createdAt: $attachment->getCreatedAt(),
            updatedAt: $attachment->getUpdatedAt()
        );
    }
}
