<?php

namespace App\DTO\Request\Identity;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'HealthcareProfessionalCreateRequestDTO',
    description: 'Données nécessaires à la création d’un professionnel de santé'
)]
class HealthcareProfessionalCreateRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 180)]
        #[OA\Property(type: 'string', format: 'email', example: 'dr.jean@diabcare.com', maxLength: 180)]
        public readonly string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        #[OA\Property(type: 'string', format: 'password', example: 'SecurePassword123!', minLength: 8)]
        public readonly string $password,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', example: 'Dr. Jean Mukendi', maxLength: 150)]
        public readonly string $fullName,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'MALE', enum: ['MALE', 'FEMALE', 'OTHER', 'UNSPECIFIED'])]
        public readonly string $gender,

        #[Assert\Length(max: 10)]
        #[OA\Property(type: 'string', example: 'fr', maxLength: 10)]
        public readonly string $locale,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', example: 'ORD-MED-2026-99', maxLength: 100)]
        public readonly string $licenseNumber,

        #[Assert\NotBlank]
        #[OA\Property(
            description: 'Détermine automatiquement le rôle de sécurité',
            type: 'string',
            example: 'CLINICIAN',
            enum: ['CLINICIAN', 'NUTRITIONIST']
        )]
        public readonly string $professionalType, // <-- Remis en string pour correspondre au JSON entrant

        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', example: '+243990000000', nullable: true, maxLength: 50)]
        public readonly ?string $phone = null,

        #[Assert\Image(
            maxSize: '2M',
            mimeTypes: ['image/jpeg', 'image/png', 'image/webp']
        )]
        #[OA\Property(description: 'Photo de profil (avatar)', type: 'string', format: 'binary', nullable: true)]
        public readonly ?UploadedFile $avatarFile = null,

        #[OA\Property(type: 'string', nullable: true)]
        public readonly ?string $avatarUrl = null,

        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', example: 'Endocrinologie et Diabétologie', nullable: true, maxLength: 150)]
        public readonly ?string $specialty = null,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', example: 'https://diabcare.com/signatures/dr-jean.png', nullable: true, maxLength: 500)]
        public readonly ?string $signatureUrl = null,

        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', nullable: true, maxLength: 255)]
        public readonly ?string $street = null,

        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', nullable: true, maxLength: 100)]
        public readonly ?string $city = null,

        #[Assert\Length(max: 20)]
        #[OA\Property(type: 'string', nullable: true, maxLength: 20)]
        public readonly ?string $postalCode = null,

        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', nullable: true, maxLength: 100)]
        public readonly ?string $country = null,
    ) {}
}
