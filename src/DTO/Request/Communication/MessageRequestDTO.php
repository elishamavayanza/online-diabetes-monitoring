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
        #[OA\Property(type: 'string', example: 'Bonjour, les analyses de biologie du patient sont disponibles.', description: 'Contenu du message')]
        public readonly string $content
    ) {}
}
