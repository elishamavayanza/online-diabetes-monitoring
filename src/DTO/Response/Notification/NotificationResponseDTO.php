<?php

namespace App\DTO\Response\Notification;

use App\Entity\Notification\Notification;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NotificationResponseDTO',
    title: 'NotificationResponseDTO',
    description: 'Structure de réponse pour une notification'
)]
class NotificationResponseDTO
{
    public function __construct(
        #[OA\Property(description: 'Identifiant unique', type: 'string', format: 'uuid', example: '77bb6655-4433-2211-0099-887766554433')]
        public readonly string $id,

        #[OA\Property(description: 'ID de l’utilisateur', type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999')]
        public readonly string $userId,

        #[OA\Property(description: 'Type de notification', type: 'string', example: 'ALERT', nullable: true)]
        public readonly ?string $type,

        #[OA\Property(description: 'Titre', type: 'string', example: 'Rappel de glycémie')]
        public readonly string $title,

        #[OA\Property(description: 'Corps du message', type: 'string', example: 'Il est l’heure...')]
        public readonly string $body,

        #[OA\Property(description: 'Canal', type: 'string', example: 'PUSH', nullable: true)]
        public readonly ?string $channel,

        #[OA\Property(description: 'Date de lecture', type: 'string', format: 'date-time', example: null, nullable: true)]
        public readonly ?\DateTimeImmutable $readAt,

        #[OA\Property(description: 'Type d’entité liée', type: 'string', example: 'BloodGlucoseMeasurement', nullable: true)]
        public readonly ?string $relatedEntityType,

        #[OA\Property(description: 'ID de l’entité liée', type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', nullable: true)]
        public readonly ?string $relatedEntityId,

        #[OA\Property(description: 'Date de création', type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(description: 'Date de mise à jour', type: 'string', format: 'date-time', example: null, nullable: true)]
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
