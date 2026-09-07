<?php

namespace App\DTO\Request\Healthcare;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'ExternalFollowCloseRequestDTO',
    description: 'Motif de fermeture d’un suivi externe par le professionnel lui-même'
)]
class ExternalFollowCloseRequestDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le motif est obligatoire.')]
        #[Assert\Length(min: 3, max: 1000, minMessage: 'Le motif doit contenir au moins 3 caractères.', maxMessage: 'Le motif ne peut pas dépasser 1000 caractères.')]
        #[OA\Property(type: 'string', example: 'Le patient est désormais suivi par mon confrère de CLINIQUE SAINT-LUC.', description: 'Motif de fermeture du suivi')]
        public readonly string $reason
    ) {}
}