<?php

namespace App\DTO\Response\Communication;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ConversationSummaryResponseDTO',
    title: 'ConversationSummaryResponseDTO',
    description: 'Résumé d\'une conversation pour la liste de messagerie'
)]
class ConversationSummaryResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string')]
        public readonly string $id,

        #[OA\Property(type: 'string')]
        public readonly string $subject,

        #[OA\Property(type: 'string')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', nullable: true)]
        public readonly ?string $patientName,

        #[OA\Property(type: 'string', nullable: true)]
        public readonly ?string $lastMessageContent,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true)]
        public readonly ?\DateTimeImmutable $lastMessageAt,

        #[OA\Property(type: 'integer')]
        public readonly int $unreadCount,

        #[OA\Property(type: 'string', format: 'date-time')]
        public readonly \DateTimeImmutable $createdAt,
    ) {}
}
