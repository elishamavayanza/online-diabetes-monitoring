<?php

namespace App\DTO\Request\Communication;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'MessageReadReceiptRequestDTO',
    description: 'Structure des données pour l’enregistrement d’un accusé de lecture'
)]
class MessageReadReceiptRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: '1', description: 'Identifiant du message')]
        public readonly string $messageId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: '1', description: 'Identifiant de l’utilisateur')]
        public readonly string $userId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:32:00Z', description: 'Date et heure de lecture')]
        public readonly \DateTimeImmutable $readAt
    ) {}
}
