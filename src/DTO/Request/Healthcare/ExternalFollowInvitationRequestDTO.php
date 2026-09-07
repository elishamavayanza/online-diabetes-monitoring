<?php

namespace App\DTO\Request\Healthcare;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'ExternalFollowInvitationRequestDTO',
    description: 'Données requises pour inviter un professionnel d’une autre organisation à suivre un patient'
)]
class ExternalFollowInvitationRequestDTO
{
    public function __construct(
        #[Assert\Positive]
        #[OA\Property(type: 'integer', format: 'int64', example: 6, description: 'Identifiant du patient à suivre')]
        public readonly int $patientId,

        #[Assert\NotBlank]
        #[Assert\Email]
        #[OA\Property(type: 'string', format: 'email', example: 'dr.dupont@centre2.com', description: 'Email du professionnel invité (un compte doit exister)')]
        public readonly string $email,

        #[Assert\NotBlank]
        #[Assert\Range(min: 1, max: 730)]
        #[OA\Property(type: 'integer', example: 90, description: 'Durée en jours de l’accès (ex. 30, 90, 180, 365)')]
        public readonly int $durationDays,

        #[OA\Property(type: 'string', nullable: true, example: 'Bonjour, merci de suivre ce patient pendant sa grossesse.', description: 'Message d’accompagnement (optionnel)')]
        public readonly ?string $message = null
    ) {}
}