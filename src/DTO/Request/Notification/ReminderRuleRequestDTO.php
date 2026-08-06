<?php

namespace App\DTO\Request\Notification;

use Symfony\Component\Validator\Constraints as Assert;

class ReminderRuleRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $patientId,

        #[Assert\NotBlank]
        public readonly mixed $targetType,

        #[Assert\Uuid]
        public readonly ?string $relatedEntityId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public readonly string $cronExpression,

        #[Assert\NotNull]
        public readonly bool $active
    ) {}
}
