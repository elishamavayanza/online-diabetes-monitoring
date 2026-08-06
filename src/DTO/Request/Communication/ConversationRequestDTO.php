<?php

namespace App\DTO\Request\Communication;

use Symfony\Component\Validator\Constraints as Assert;

class ConversationRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $subject,

        public readonly ?string $organizationId,

        #[Assert\NotBlank]
        public readonly string $createdById,

        public readonly ?\DateTimeImmutable $closedAt
    ) {}
}
