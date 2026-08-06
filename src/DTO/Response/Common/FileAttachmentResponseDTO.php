<?php

namespace App\DTO\Response\Common;

use App\Entity\Common\FileAttachment;
use Symfony\Component\Uid\Uuid;

class FileAttachmentResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $originalName,
        public readonly string $fileName,
        public readonly string $mimeType,
        public readonly int $sizeBytes,
        public readonly string $url,
        public readonly string $entityType,
        public readonly Uuid $entityId,
        public readonly string $uploadedById,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(FileAttachment $fileAttachment): self
    {
        return new self(
            id: (string) $fileAttachment->getId(),
            originalName: $fileAttachment->getOriginalName(),
            fileName: $fileAttachment->getFileName(),
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
