<?php

namespace App\DTO\Request\Communication;

use Symfony\Component\Validator\Constraints as Assert;

class MessageRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $conversationId,

        #[Assert\NotBlank]
        public readonly string $senderId,

        #[Assert\NotBlank]
        public readonly string $content,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $sentAt,

        public readonly ?\DateTimeImmutable $editedAt
    ) {}
}
