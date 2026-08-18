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
        #[OA\Property(description: 'Objet de la conversation', type: 'string', example: 'Suivi post-opératoire du dossier patient #42', maxLength: 255)]
        public readonly string $subject,

        #[Assert\NotBlank]
        #[OA\Property(description: 'Identifiant du patient concerné', type: 'string', example: '1')]
        public readonly string $patientId,

        #[OA\Property(description: 'Identifiant de l’organisation de santé', type: 'string', example: '1', nullable: true)]
        public readonly ?string $organizationId,

        #[OA\Property(description: 'Date de fermeture initiale', type: 'string', format: 'date-time', example: null, nullable: true)]
        public readonly ?\DateTimeImmutable $closedAt
    ) {}
}
