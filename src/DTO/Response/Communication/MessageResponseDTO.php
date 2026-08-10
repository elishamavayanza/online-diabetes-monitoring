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
        #[OA\Property(type: 'string', format: 'uuid', example: '9f881245-33ee-4b11-9a21-4f88e1478c99', description: 'Identifiant unique du message')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant de la conversation')]
        public readonly string $conversationId,

        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant de l’expéditeur')]
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
