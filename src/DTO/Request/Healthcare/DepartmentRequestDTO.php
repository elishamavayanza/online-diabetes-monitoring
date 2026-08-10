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
        #[OA\Property(type: 'string', format: 'uuid', example: '11bb22cc-33ee-4ff1-8811-9a8877665544', description: 'Identifiant de l’établissement')]
        public readonly string $facilityId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'Cardiologie', description: 'Nom du département')]
        public readonly string $name,

        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, nullable: true, example: 'Cardiologie interventionnelle', description: 'Spécialité')]
        public readonly ?string $specialty
    ) {}
}
