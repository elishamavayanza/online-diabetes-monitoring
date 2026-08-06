<?php

namespace App\DTO\Request\Communication;

use Symfony\Component\Validator\Constraints as Assert;

class ConversationParticipantRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $conversationId,

        #[Assert\NotBlank]
        public readonly string $userId,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $joinedAt,

        public readonly ?\DateTimeImmutable $leftAt
    ) {}
}
