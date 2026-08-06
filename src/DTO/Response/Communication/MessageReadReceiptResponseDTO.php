<?php

namespace App\DTO\Response\Communication;

use App\Entity\Communication\MessageReadReceipt;

class MessageReadReceiptResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $messageId,
        public readonly string $participantId,
        public readonly \DateTimeImmutable $readAt,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(MessageReadReceipt $receipt): self
    {
        return new self(
            id: (string) $receipt->getId(),
            messageId: (string) $receipt->getMessage()?->getId(),
            participantId: (string) $receipt->getParticipant()?->getId(),
            readAt: $receipt->getReadAt(),
            createdAt: $receipt->getCreatedAt(),
            updatedAt: $receipt->getUpdatedAt()
        );
    }
}
