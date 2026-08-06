<?php

namespace App\DTO\Response\Notification;

use App\Entity\Notification\ReminderRule;

class ReminderRuleResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $patientId,
        public readonly ?string $targetType,
        public readonly ?string $relatedEntityId,
        public readonly ?string $cronExpression,
        public readonly bool $active,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(ReminderRule $rule): self
    {
        return new self(
            id: (string) $rule->getId(),
            patientId: (string) $rule->getPatient()?->getId(),
            targetType: $rule->getTargetType()?->value,
            relatedEntityId: $rule->getRelatedEntityId(),
            cronExpression: $rule->getCronExpression(),
            active: $rule->isActive(),
            createdAt: $rule->getCreatedAt(),
            updatedAt: $rule->getUpdatedAt()
        );
    }
}
