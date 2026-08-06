<?php

namespace App\DTO\Response\Communication;

use App\Entity\Communication\Message;

class MessageResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $conversationId,
        public readonly string $senderId,
        public readonly string $content,
        public readonly \DateTimeImmutable $sentAt,
        public readonly ?\DateTimeImmutable $editedAt,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Message $message): self
    {
        return new self(
            id: (string) $message->getId(),
            conversationId: (string) $message->getConversation()?->getId(),
            senderId: (string) $message->getSender()?->getId(),
            content: $message->getContent(),
            sentAt: $message->getSentAt(),
            editedAt: $message->getEditedAt(),
            createdAt: $message->getCreatedAt(),
            updatedAt: $message->getUpdatedAt()
        );
    }
}
