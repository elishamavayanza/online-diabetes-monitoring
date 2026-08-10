<?php

namespace App\DTO\Request\Healthcare;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'HealthcareOrganizationRequestDTO',
    description: 'Structure des données requises pour la création d’une organisation de santé'
)]
class HealthcareOrganizationRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'DiabCare Health Group', description: 'Nom de l’organisation')]
        public readonly string $name,

        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', maxLength: 50, nullable: true, example: 'DHG', description: 'Nom court')]
        public readonly ?string $shortName,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'HOSPITAL_NETWORK', description: 'Type d’organisation')]
        public readonly mixed $type,

        #[Assert\Email]
        #[Assert\Length(max: 180)]
        #[OA\Property(type: 'string', format: 'email', maxLength: 180, nullable: true, example: 'contact@diabcare.com', description: 'E-mail')]
        public readonly ?string $email,

        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', maxLength: 50, nullable: true, example: '+243990000000', description: 'Téléphone')]
        public readonly ?string $phone,

        #[Assert\Url]
        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', format: 'uri', maxLength: 255, nullable: true, example: 'https://www.diabcare.com', description: 'Site Web')]
        public readonly ?string $website,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://storage.diabcare.com/logos/dhg.png', description: 'Logo URL')]
        public readonly ?string $logoUrl,

        #[OA\Property(type: 'object', nullable: true, description: 'Adresse postale')]
        public readonly ?array $address,

        #[Assert\NotNull]
        #[OA\Property(type: 'boolean', example: true, description: 'Statut actif')]
        public readonly bool $active
    ) {}
}
