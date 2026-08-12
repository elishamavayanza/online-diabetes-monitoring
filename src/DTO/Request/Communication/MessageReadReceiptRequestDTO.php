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
        #[OA\Property(type: 'string', format: 'uuid', example: '9f881245-33ee-4b11-9a21-4f88e1478c99', description: 'Identifiant du message')]
        public readonly string $messageId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant de l’utilisateur')]
        public readonly string $userId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:32:00Z', description: 'Date et heure de lecture')]
        public readonly \DateTimeImmutable $readAt
    ) {}
}
