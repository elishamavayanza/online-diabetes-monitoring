<?php

namespace App\DTO\Response\Communication;

use App\Entity\Communication\Conversation;

class ConversationResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $subject,
        public readonly ?string $organizationId,
        public readonly string $createdById,
        public readonly ?\DateTimeImmutable $closedAt,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Conversation $conversation): self
    {
        return new self(
            id: (string) $conversation->getId(),
            subject: $conversation->getSubject(),
            organizationId: $conversation->getOrganization()?->getId() ? (string) $conversation->getOrganization()->getId() : null,
            createdById: (string) $conversation->getCreatedBy()?->getId(),
            closedAt: $conversation->getClosedAt(),
            createdAt: $conversation->getCreatedAt(),
            updatedAt: $conversation->getUpdatedAt()
        );
    }
}
