<?php

namespace App\DTO\Request\Notification;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'NotificationRequestDTO',
    description: 'Structure de requête pour la création d’une ou plusieurs notifications'
)]
class NotificationRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['USER', 'ORGANIZATION', 'GLOBAL'], message: 'Le scope doit être USER, ORGANIZATION ou GLOBAL.')]
        #[OA\Property(description: 'Portée de la notification (USER, ORGANIZATION, GLOBAL)', type: 'string', example: 'USER')]
        public readonly string $scope,

        #[OA\Property(description: 'Requis si scope = USER', type: 'string', format: 'uuid', example: '19', nullable: true)]
        public readonly ?string $userId = null,

        #[OA\Property(description: 'Requis si scope = ORGANIZATION', type: 'string', format: 'uuid', example: 'org-uuid-1234', nullable: true)]
        public readonly ?string $organizationId = null,

        #[Assert\NotBlank]
        #[OA\Property(description: 'Type de notification', type: 'string', example: 'ALERT')]
        public readonly mixed $type,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'Titre', type: 'string', example: 'Rappel important', maxLength: 255)]
        public readonly string $title,

        #[Assert\NotBlank]
        #[OA\Property(description: 'Corps du message', type: 'string', example: 'Il est l’heure...')]
        public readonly string $body,

        #[Assert\NotBlank]
        #[OA\Property(description: 'Canal de diffusion', type: 'string', example: 'PUSH')]
        public readonly mixed $channel,

        #[OA\Property(description: 'Date de lecture', type: 'string', format: 'date-time', example: null, nullable: true)]
        public readonly ?\DateTimeImmutable $readAt = null,

        #[Assert\Length(max: 150)]
        #[OA\Property(description: 'Type d’entité liée', type: 'string', example: 'SystemEvent', nullable: true, maxLength: 150)]
        public readonly ?string $relatedEntityType = null,

        #[Assert\Uuid]
        #[OA\Property(description: 'ID de l’entité liée', type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', nullable: true)]
        public readonly ?string $relatedEntityId = null
    ) {}
}
