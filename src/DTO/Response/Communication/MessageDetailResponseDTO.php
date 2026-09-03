<?php

namespace App\DTO\Response\Communication;

use App\Entity\Communication\Message;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MessageDetailResponseDTO',
    title: 'MessageDetailResponseDTO',
    description: 'Message avec statut de lecture pour l\'interface de messagerie'
)]
class MessageDetailResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string')]
        public readonly string $id,

        #[OA\Property(type: 'string')]
        public readonly string $conversationId,

        #[OA\Property(type: 'string')]
        public readonly string $senderId,

        #[OA\Property(type: 'string', nullable: true)]
        public readonly ?string $authorName,   // ✅ nouveau champ

        #[OA\Property(type: 'string')]
        public readonly string $content,

        #[OA\Property(type: 'string', format: 'date-time')]
        public readonly \DateTimeImmutable $sentAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true)]
        public readonly ?\DateTimeImmutable $editedAt,

        #[OA\Property(type: 'boolean')]
        public readonly bool $isMine,

        #[OA\Property(type: 'boolean')]
        public readonly bool $isRead,

        /** @var MessageAttachmentResponseDTO[] */
        #[OA\Property(type: 'array', items: new OA\Items(ref: new \Nelmio\ApiDocBundle\Attribute\Model(type: MessageAttachmentResponseDTO::class)))]
        public readonly array $attachments,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true)]
        public readonly ?\DateTimeImmutable $readAt,
    ) {}

    /** @param MessageAttachmentResponseDTO[] $attachments */
    public static function fromEntity(
        Message $message,
        bool $isMine,
        bool $isRead,
        ?\DateTimeImmutable $readAt,
        array $attachments = [],
        ?string $authorName = null   // ✅ paramètre optionnel
    ): self {
        return new self(
            id: (string) $message->getId(),
            conversationId: (string) $message->getConversation()?->getId(),
            senderId: (string) $message->getSender()?->getId(),
            authorName: $authorName,   // ✅ transmettre le nom
            content: $message->getContent(),
            sentAt: $message->getSentAt(),
            editedAt: $message->getEditedAt(),
            isMine: $isMine,
            isRead: $isRead,
            attachments: $attachments,
            readAt: $readAt,
        );
    }
}
