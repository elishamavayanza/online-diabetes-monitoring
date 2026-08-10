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
        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant de la conversation')]
        public readonly string $conversationId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant de l’expéditeur')]
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
