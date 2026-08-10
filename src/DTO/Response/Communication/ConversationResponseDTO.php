<?php

namespace App\DTO\Response\Communication;

use App\Entity\Communication\Conversation;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'ConversationResponseDTO',
    description: 'Structure des données renvoyées pour une conversation'
)]
class ConversationResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant unique de la conversation')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: 'Suivi post-opératoire du dossier patient #42', description: 'Objet de la conversation')]
        public readonly string $subject,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '1c552144-88ef-4a92-b4c4-7893a12b4e55', description: 'Identifiant de l’organisation')]
        public readonly ?string $organizationId,

        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant du créateur')]
        public readonly string $createdById,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de fermeture')]
        public readonly ?\DateTimeImmutable $closedAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T09:00:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de dernière modification')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Conversation $conversation): self
    {
        return new self(
            id: (string) $conversation->getId(),
            subject: $conversation->getSubject(),
            organizationId: $conversation->getOrganization()?->getId() ? (string) $conversation->getOrganization()->getId() : null,
            createdById: (string) $conversation->getCreatedBy()?->getId(),
            closedAt: $conversation->getClosedAt(),
            createdAt: $conversation->getCreatedAt(),
            updatedAt: $conversation->getUpdatedAt()
        );
    }
}
