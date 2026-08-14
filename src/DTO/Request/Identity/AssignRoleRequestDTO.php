<?php

namespace App\DTO\Request\Identity;

use App\Entity\Identity\Role;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'AssignRoleRequestDTO',
    description: 'Données nécessaires pour attribuer un rôle à un utilisateur'
)]
class AssignRoleRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [Role::class, 'values'])]
        #[OA\Property(
            description: 'Rôle de sécurité à attribuer',
            type: 'string',
            example: 'ROLE_CLINICIAN'
        )]
        public readonly string $role
    ) {}
}
