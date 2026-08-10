<?php

namespace App\DTO\Request\Communication;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'ConversationRequestDTO',
    description: 'Structure des données requises pour la création d’une conversation'
)]
class ConversationRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', maxLength: 255, example: 'Suivi post-opératoire du dossier patient #42', description: 'Objet de la conversation')]
        public readonly string $subject,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '1c552144-88ef-4a92-b4c4-7893a12b4e55', description: 'Identifiant de l’organisation')]
        public readonly ?string $organizationId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant de l’utilisateur créateur')]
        public readonly string $createdById,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de fermeture initiale')]
        public readonly ?\DateTimeImmutable $closedAt
    ) {}
}
