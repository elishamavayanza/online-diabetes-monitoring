<?php

namespace App\DTO\Request\Identity;

use App\Entity\Common\Gender;
use App\Entity\Identity\ProfessionalType;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'HealthcareProfessionalRequestDTO',
    description: 'Structure des données requises pour la création d’un professionnel de santé'
)]
class HealthcareProfessionalRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 180)]
        #[OA\Property(type: 'string', format: 'email', maxLength: 180, example: 'dr.jean@diabcare.com', description: 'E-mail')]
        public readonly string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        #[OA\Property(type: 'string', minLength: 8, example: 'SecurePassword123!', description: 'Mot de passe')]
        public readonly string $password,

        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', maxLength: 50, nullable: true, example: '+243990000000', description: 'Téléphone')]
        public readonly ?string $phone,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'Dr. Jean Mukendi', description: 'Nom complet')]
        public readonly string $fullName,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://storage.diabcare.com/avatars/jean.jpg', description: 'Avatar URL')]
        public readonly ?string $avatarUrl,

        #[Assert\NotNull]
        #[OA\Property(type: 'string', example: 'MALE', description: 'Genre')]
        public readonly Gender $gender,

        #[Assert\Length(max: 10)]
        #[OA\Property(type: 'string', maxLength: 10, example: 'fr', description: 'Locale')]
        public readonly string $locale,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, example: 'ORD-MED-2026-99', description: 'Numéro de licence')]
        public readonly string $licenseNumber,

        #[Assert\NotNull]
        #[OA\Property(type: 'string', example: 'CLINICIAN', description: 'Type de professionnel')]
        public readonly ProfessionalType $professionalType,

        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, nullable: true, example: 'Endocrinologie et Diabétologie', description: 'Spécialité')]
        public readonly ?string $specialty,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://storage.diabcare.com/sigs/jean.png', description: 'URL de la signature')]
        public readonly ?string $signatureUrl,

        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', maxLength: 255, nullable: true, example: '12 Avenue de la Paix', description: 'Rue')]
        public readonly ?string $street,

        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, nullable: true, example: 'Goma', description: 'Ville')]
        public readonly ?string $city,

        #[Assert\Length(max: 20)]
        #[OA\Property(type: 'string', maxLength: 20, nullable: true, example: '00243', description: 'Code postal')]
        public readonly ?string $postalCode,

        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, nullable: true, example: 'RDC', description: 'Pays')]
        public readonly ?string $country
    ) {}
}
