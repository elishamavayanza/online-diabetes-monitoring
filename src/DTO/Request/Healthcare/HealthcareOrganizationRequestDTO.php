<?php

namespace App\DTO\Request\Healthcare;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        #[OA\Property(description: 'Nom de l’organisation', type: 'string', example: 'DiabCare Health Group', maxLength: 150)]
        public readonly string $name,

        #[Assert\Length(max: 50)]
        #[OA\Property(description: 'Nom court', type: 'string', example: 'DHG', nullable: true, maxLength: 50)]
        public readonly ?string $shortName,

        #[Assert\NotBlank]
        #[OA\Property(description: 'Type d’organisation', type: 'string', example: 'NETWORK')]
        public readonly mixed $type,

        #[Assert\Email]
        #[Assert\Length(max: 180)]
        #[OA\Property(description: 'E-mail', type: 'string', format: 'email', example: 'contact@diabcare.com', nullable: true, maxLength: 180)]
        public readonly ?string $email,

        #[Assert\Length(max: 50)]
        #[OA\Property(description: 'Téléphone', type: 'string', example: '+243990000000', nullable: true, maxLength: 50)]
        public readonly ?string $phone,

        #[Assert\Url]
        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'Site Web', type: 'string', format: 'uri', example: 'https://www.diabcare.com', nullable: true, maxLength: 255)]
        public readonly ?string $website,

        // Modification ici pour accepter le fichier uploadé
        #[Assert\File(
            maxSize: '2M',
            mimeTypes: ['image/jpeg', 'image/png', 'image/webp']
        )]
        #[OA\Property(description: 'Fichier logo de l’organisation', type: 'string', format: 'binary', nullable: true)]
        public readonly ?UploadedFile $logoFile,

        #[OA\Property(description: 'Adresse postale', type: 'object', nullable: true)]
        public readonly ?array $address,

        #[Assert\NotNull]
        #[OA\Property(description: 'Statut actif', type: 'boolean', example: true)]
        public readonly bool $active
    ) {}
}
