<?php

namespace App\DTO\Response\Notification;

use App\Entity\Notification\Notification;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'NotificationResponseDTO',
    description: 'Structure de réponse pour une notification'
)]
class NotificationResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '77bb6655-4433-2211-0099-887766554433', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID de l’utilisateur')]
        public readonly string $userId,

        #[OA\Property(type: 'string', nullable: true, example: 'ALERT', description: 'Type de notification')]
        public readonly ?string $type,

        #[OA\Property(type: 'string', example: 'Rappel de glycémie', description: 'Titre')]
        public readonly string $title,

        #[OA\Property(type: 'string', example: 'Il est l’heure...', description: 'Corps du message')]
        public readonly string $body,

        #[OA\Property(type: 'string', nullable: true, example: 'PUSH', description: 'Canal')]
        public readonly ?string $channel,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de lecture')]
        public readonly ?\DateTimeImmutable $readAt,

        #[OA\Property(type: 'string', nullable: true, example: 'BloodGlucoseMeasurement', description: 'Type d’entité liée')]
        public readonly ?string $relatedEntityType,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID de l’entité liée')]
        public readonly ?string $relatedEntityId,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
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
