<?php

namespace App\DTO\Request\Notification;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'NotificationRequestDTO',
    description: 'Structure de requête pour la création d’une notification'
)]
class NotificationRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'Identifiant de l’utilisateur')]
        public readonly string $userId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'ALERT', description: 'Type de notification')]
        public readonly mixed $type,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', maxLength: 255, example: 'Rappel de glycémie', description: 'Titre')]
        public readonly string $title,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'Il est l’heure...', description: 'Corps du message')]
        public readonly string $body,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'PUSH', description: 'Canal de diffusion')]
        public readonly mixed $channel,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de lecture')]
        public readonly ?\DateTimeImmutable $readAt,

        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, nullable: true, example: 'BloodGlucoseMeasurement', description: 'Type d’entité liée')]
        public readonly ?string $relatedEntityType,

        #[Assert\Uuid]
        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID de l’entité liée')]
        public readonly ?string $relatedEntityId
    ) {}
}
