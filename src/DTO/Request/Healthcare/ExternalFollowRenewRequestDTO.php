<?php

namespace App\DTO\Request\Healthcare;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'ExternalFollowRenewRequestDTO',
    description: 'Renouvellement d’un suivi externe : définition d’une nouvelle période (de/à) ou ajout de jours'
)]
class ExternalFollowRenewRequestDTO
{
    public function __construct(
        #[Assert\Range(min: 1, max: 730)]
        #[OA\Property(type: 'integer', example: 90, description: 'Nombre de jours ajoutés au délai courant. Ignoré si endDate est fournie.')]
        public readonly ?int $durationDays = null,

        #[OA\Property(type: 'string', format: 'date', nullable: true, example: '2026-10-01', description: 'Date de début de la nouvelle période (utilisée avec endDate pour une période personnalisée)')]
        public readonly ?\DateTimeImmutable $startDate = null,

        #[OA\Property(type: 'string', format: 'date', nullable: true, example: '2027-01-01', description: 'Date de fin de la nouvelle période (utilisée avec startDate pour une période personnalisée)')]
        public readonly ?\DateTimeImmutable $endDate = null
    ) {}
}