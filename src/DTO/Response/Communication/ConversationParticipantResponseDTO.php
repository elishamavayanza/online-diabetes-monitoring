<?php

namespace App\DTO\Response\Communication;

use App\Entity\Communication\ConversationParticipant;

class ConversationParticipantResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $conversationId,
        public readonly string $userId,
        public readonly \DateTimeImmutable $joinedAt,
        public readonly ?\DateTimeImmutable $leftAt,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(ConversationParticipant $participant): self
    {
        return new self(
            id: (string) $participant->getId(),
            conversationId: (string) $participant->getConversation()?->getId(),
            userId: (string) $participant->getUser()?->getId(),
            joinedAt: $participant->getJoinedAt(),
            leftAt: $participant->getLeftAt(),
            createdAt: $participant->getCreatedAt(),
            updatedAt: $participant->getUpdatedAt()
        );
    }
}
