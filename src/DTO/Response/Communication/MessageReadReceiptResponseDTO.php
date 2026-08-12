<?php

namespace App\DTO\Response\Communication;

use App\Entity\Communication\MessageReadReceipt;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MessageReadReceiptResponseDTO',
    title: 'MessageReadReceiptResponseDTO',
    description: 'Structure des données renvoyées pour un accusé de lecture'
)]
class MessageReadReceiptResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '11bb22cc-33ee-4ff1-8811-9a8877665544', description: 'Identifiant unique de l’accusé de lecture')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '9f881245-33ee-4b11-9a21-4f88e1478c99', description: 'Identifiant du message')]
        public readonly string $messageId,

        #[OA\Property(type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant de l’utilisateur')]
        public readonly string $userId,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:32:00Z', description: 'Date et heure de lecture')]
        public readonly \DateTimeImmutable $readAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:32:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(MessageReadReceipt $receipt): self
    {
        return new self(
            id: (string) $receipt->getId(),
            messageId: (string) $receipt->getMessage()?->getId(),
            userId: (string) $receipt->getUser()?->getId(),
            readAt: $receipt->getReadAt(),
            createdAt: $receipt->getCreatedAt(),
            updatedAt: $receipt->getUpdatedAt()
        );
    }
}
