<?php

namespace App\DTO\Request\Healthcare;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'ExternalFollowRenewRequestDTO',
    description: 'Durée d’extension d’un suivi externe (renouvellement)'
)]
class ExternalFollowRenewRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Range(min: 1, max: 730)]
        #[OA\Property(type: 'integer', example: 90, description: 'Nombre de jours ajoutés au délai courant')]
        public readonly int $durationDays
    ) {}
}