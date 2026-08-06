<?php

namespace App\DTO\Request\Notification;

use Symfony\Component\Validator\Constraints as Assert;

class NotificationRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $userId,

        #[Assert\NotBlank]
        public readonly mixed $type,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $title,

        #[Assert\NotBlank]
        public readonly string $body,

        #[Assert\NotBlank]
        public readonly mixed $channel,

        public readonly ?\DateTimeImmutable $readAt,

        #[Assert\Length(max: 150)]
        public readonly ?string $relatedEntityType,

        #[Assert\Uuid]
        public readonly ?string $relatedEntityId
    ) {}
}
