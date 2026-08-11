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
        #[OA\Property(type: 'string', example: '1', description: 'Identifiant unique de la conversation')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: 'Suivi post-opératoire du dossier patient #42', description: 'Objet de la conversation')]
        public readonly string $subject,

        #[OA\Property(type: 'string', example: '1', description: 'Identifiant du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', nullable: true, example: '1', description: 'Identifiant de l’organisation')]
        public readonly ?string $organizationId,

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
            patientId: (string) $conversation->getPatient()->getId(),
            organizationId: $conversation->getOrganization()?->getId() ? (string) $conversation->getOrganization()->getId() : null,
            closedAt: $conversation->getClosedAt(),
            createdAt: $conversation->getCreatedAt(),
            updatedAt: $conversation->getUpdatedAt()
        );
    }
}
