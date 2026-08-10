<?php

namespace App\DTO\Request\Patient;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'EmergencyContactRequestDTO',
    description: 'Structure de requête pour la création d’un contact d’urgence'
)]
class EmergencyContactRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient')]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'Marie Dupont', description: 'Nom complet')]
        public readonly string $fullName,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, example: 'Conjointe', description: 'Relation')]
        public readonly string $relationship,

        #[Assert\NotBlank]
        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', maxLength: 50, example: '+243900000000', description: 'Téléphone')]
        public readonly string $phone,

        #[Assert\Email]
        #[Assert\Length(max: 180)]
        #[OA\Property(type: 'string', format: 'email', maxLength: 180, nullable: true, example: 'marie@example.com', description: 'Email')]
        public readonly ?string $email
    ) {}
}
