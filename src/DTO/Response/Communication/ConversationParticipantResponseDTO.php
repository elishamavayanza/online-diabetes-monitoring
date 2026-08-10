<?php

namespace App\DTO\Response\Communication;

use App\Entity\Communication\ConversationParticipant;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'ConversationParticipantResponseDTO',
    description: 'Structure des données renvoyées pour un participant de conversation'
)]
class ConversationParticipantResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant unique de la participation')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant de la conversation')]
        public readonly string $conversationId,

        #[OA\Property(type: 'string', format: 'uuid', example: '4a613328-98e3-4d64-8898-0c06a3861c8f', description: 'Identifiant de l’utilisateur')]
        public readonly string $userId,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T09:15:00Z', description: 'Date d’adhésion')]
        public readonly \DateTimeImmutable $joinedAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de départ')]
        public readonly ?\DateTimeImmutable $leftAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T09:15:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(ConversationParticipant $participant): self
    {
        return new self(
            id: (string) $participant->getId(),
            conversationId: (string) $participant->getConversation()?->getId(),
            userId: (string) $participant->getUser()?->getId(),
            joinedAt: $participant->getJoinedAt(),
            leftAt: $participant->getLeftAt(),
            createdAt: $participant->getCreatedAt(),
            updatedAt: $participant->getUpdatedAt()
        );
    }
}
