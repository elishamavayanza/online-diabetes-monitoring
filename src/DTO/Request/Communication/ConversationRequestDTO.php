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

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: '1', description: 'Identifiant du patient concerné')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', nullable: true, example: '1', description: 'Identifiant de l’organisation de santé')]
        public readonly ?string $organizationId,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de fermeture initiale')]
        public readonly ?\DateTimeImmutable $closedAt
    ) {}
}
