<?php

namespace App\DTO\Request\Healthcare;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'HealthcareFacilityRequestDTO',
    description: 'Structure des données requises pour la création d’un établissement de santé'
)]
class HealthcareFacilityRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant de l’organisation')]
        public readonly string $organizationId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'Hôpital Général de Référence', description: 'Nom de l’établissement')]
        public readonly string $name,

        #[OA\Property(type: 'object', nullable: true, description: 'Adresse postale')]
        public readonly ?array $address,

        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', maxLength: 50, nullable: true, example: '+243990000000', description: 'Téléphone')]
        public readonly ?string $phone
    ) {}
}
