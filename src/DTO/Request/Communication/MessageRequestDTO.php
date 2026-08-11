<?php

namespace App\DTO\Request\Communication;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'MessageRequestDTO',
    description: 'Structure des données requises pour l’envoi d’un message'
)]
class MessageRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: '1', description: 'Identifiant de la conversation')]
        public readonly string $conversationId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: '1', description: 'Identifiant de l’expéditeur')]
        public readonly string $senderId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'Bonjour, les analyses de biologie du patient sont disponibles.', description: 'Contenu du message')]
        public readonly string $content,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date et heure d’envoi')]
        public readonly \DateTimeImmutable $sentAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de modification éventuelle')]
        public readonly ?\DateTimeImmutable $editedAt
    ) {}
}
