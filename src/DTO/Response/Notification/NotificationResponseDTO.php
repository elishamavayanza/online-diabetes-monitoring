<?php

namespace App\DTO\Response\Notification;

use App\Entity\Notification\Notification;

class NotificationResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly ?string $type,
        public readonly string $title,
        public readonly string $body,
        public readonly ?string $channel,
        public readonly ?\DateTimeImmutable $readAt,
        public readonly ?string $relatedEntityType,
        public readonly ?string $relatedEntityId,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Notification $notification): self
    {
        return new self(
            id: (string) $notification->getId(),
            userId: (string) $notification->getUser()?->getId(),
            type: $notification->getType()?->value,
            title: $notification->getTitle(),
            body: $notification->getBody(),
            channel: $notification->getChannel()?->value,
            readAt: $notification->getReadAt(),
            relatedEntityType: $notification->getRelatedEntityType(),
            relatedEntityId: $notification->getRelatedEntityId(),
            createdAt: $notification->getCreatedAt(),
            updatedAt: $notification->getUpdatedAt()
        );
    }
}
