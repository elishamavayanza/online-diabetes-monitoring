<?php

namespace App\DTO\Request\Patient;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'AllergyRequestDTO',
    description: 'Structure de requête pour la création d’une allergie'
)]
class AllergyRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient')]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'Pénicilline', description: 'Nom de l’allergène')]
        public readonly string $name,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'SEVERE', description: 'Sévérité')]
        public readonly mixed $severity,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Choc anaphylactique', description: 'Réaction')]
        public readonly ?string $reaction,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Notes additionnelles', description: 'Notes')]
        public readonly ?string $notes,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date du diagnostic')]
        public readonly \DateTimeImmutable $diagnosedAt
    ) {}
}
