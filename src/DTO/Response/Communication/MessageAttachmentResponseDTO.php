<?php

namespace App\DTO\Response\Communication;

use App\Entity\Communication\MessageAttachment;

class MessageAttachmentResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $messageId,
        public readonly string $fileUrl,
        public readonly string $fileName,
        public readonly string $mimeType,
        public readonly int $sizeBytes,
        public readonly \DateTimeImmutable $createdAt,
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
