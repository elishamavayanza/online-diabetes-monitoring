<?php

namespace App\DTO\Response\Notification;

use App\Entity\Notification\ReminderRule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'ReminderRuleResponseDTO',
    description: 'Structure de réponse pour une règle de rappel'
)]
class ReminderRuleResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '44cc3322-1100-9988-7766-554433221100', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', nullable: true, example: 'BLOOD_GLUCOSE', description: 'Type de cible')]
        public readonly ?string $targetType,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID de l’entité liée')]
        public readonly ?string $relatedEntityId,

        #[OA\Property(type: 'string', nullable: true, example: '0 8 * * *', description: 'Expression Cron')]
        public readonly ?string $cronExpression,

        #[OA\Property(type: 'boolean', example: true, description: 'Statut actif')]
        public readonly bool $active,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
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
