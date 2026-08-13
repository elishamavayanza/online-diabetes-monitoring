<?php

namespace App\DTO\Request\Healthcare;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'DepartmentRequestDTO',
    description: 'Structure des données requises pour la création d’un département médical'
)]
class DepartmentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(description: 'Identifiant de l’établissement', type: 'string', format: 'uuid', example: '1')]
        public readonly string $facilityId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(description: 'Nom du département', type: 'string', example: 'Cardiologie', maxLength: 150)]
        public readonly string $name,

        #[Assert\Length(max: 150)]
        #[OA\Property(description: 'Spécialité', type: 'string', example: 'Cardiologie interventionnelle', nullable: true, maxLength: 150)]
        public readonly ?string $specialty
    ) {}
}
