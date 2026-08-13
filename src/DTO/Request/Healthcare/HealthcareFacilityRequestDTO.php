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
        #[OA\Property(description: 'Identifiant de l’organisation', type: 'string', format: 'uuid', example: '1')]
        public readonly string $organizationId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(description: 'Nom de l’établissement', type: 'string', example: 'Hôpital Général de Référence', maxLength: 150)]
        public readonly string $name,

        #[OA\Property(description: 'Adresse postale', type: 'object', nullable: true)]
        public readonly ?array $address,

        #[Assert\Length(max: 50)]
        #[OA\Property(description: 'Téléphone', type: 'string', example: '+243990000000', nullable: true, maxLength: 50)]
        public readonly ?string $phone
    ) {}
}
