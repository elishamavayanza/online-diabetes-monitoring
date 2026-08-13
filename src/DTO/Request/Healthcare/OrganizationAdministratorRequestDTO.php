<?php

namespace App\DTO\Request\Healthcare;

use App\Entity\Common\Gender;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'OrganizationAdministratorRequestDTO',
    description: 'Données nécessaires pour créer un administrateur rattaché à une organisation'
)]
class OrganizationAdministratorRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 180)]
        #[OA\Property(type: 'string', format: 'email', example: 'admin@clinique.cd')]
        public readonly string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        #[OA\Property(type: 'string', minLength: 8, example: 'SecurePassword123!')]
        public readonly string $password,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', example: 'Marie Kasongo')]
        public readonly string $fullName,

        #[Assert\NotNull]
        #[OA\Property(type: 'string', example: 'FEMALE')]
        public readonly Gender $gender,

        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', nullable: true, example: '+243990000000')]
        public readonly ?string $phone = null,

        #[Assert\Length(max: 10)]
        #[OA\Property(type: 'string', example: 'fr')]
        public readonly string $locale = 'fr',

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', nullable: true)]
        public readonly ?string $avatarUrl = null,

        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', nullable: true)]
        public readonly ?string $street = null,

        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', nullable: true)]
        public readonly ?string $city = null,

        #[Assert\Length(max: 20)]
        #[OA\Property(type: 'string', nullable: true)]
        public readonly ?string $postalCode = null,

        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', nullable: true)]
        public readonly ?string $country = null,
    ) {}
}
