<?php

namespace App\DTO\Request\Communication;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'ConversationParticipantRequestDTO',
    description: 'Structure des données requises pour l’ajout d’un participant à une conversation'
)]
class ConversationParticipantRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant de la conversation')]
        public readonly string $conversationId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '4a613328-98e3-4d64-8898-0c06a3861c8f', description: 'Identifiant de l’utilisateur')]
        public readonly string $userId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T09:15:00Z', description: 'Date et heure d’adhésion')]
        public readonly \DateTimeImmutable $joinedAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de départ')]
        public readonly ?\DateTimeImmutable $leftAt
    ) {}
}
