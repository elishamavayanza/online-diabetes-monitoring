<?php

namespace App\DTO\Response\Communication;

use App\Entity\Communication\Message;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'MessageResponseDTO',
    description: 'Structure des données renvoyées pour un message'
)]
class MessageResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', example: '1', description: 'Identifiant unique du message')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: '1', description: 'Identifiant de la conversation')]
        public readonly string $conversationId,

        #[OA\Property(type: 'string', example: '1', description: 'Identifiant de l’expéditeur')]
        public readonly string $senderId,

        #[OA\Property(type: 'string', example: 'Bonjour, les analyses de biologie du patient sont disponibles.', description: 'Contenu')]
        public readonly string $content,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date d’envoi')]
        public readonly \DateTimeImmutable $sentAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de modification')]
        public readonly ?\DateTimeImmutable $editedAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Message $message): self
    {
        return new self(
            id: (string) $message->getId(),
            conversationId: (string) $message->getConversation()?->getId(),
            senderId: (string) $message->getSender()?->getId(),
            content: $message->getContent(),
            sentAt: $message->getSentAt(),
            editedAt: $message->getEditedAt(),
            createdAt: $message->getCreatedAt(),
            updatedAt: $message->getUpdatedAt()
        );
    }
}
