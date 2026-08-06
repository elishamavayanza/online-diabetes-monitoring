<?php

namespace App\DTO\Request\Communication;

use Symfony\Component\Validator\Constraints as Assert;

class MessageReadReceiptRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $messageId,

        #[Assert\NotBlank]
        public readonly string $participantId,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $readAt
    ) {}
}
