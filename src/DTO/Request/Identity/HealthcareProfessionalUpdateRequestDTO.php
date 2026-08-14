<?php

namespace App\DTO\Request\Identity;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'HealthcareProfessionalUpdateRequestDTO',
    description: 'Données optionnelles pour la mise à jour partielle ou complète d’un professionnel de santé'
)]
class HealthcareProfessionalUpdateRequestDTO
{
    public function __construct(
        #[Assert\Email]
        #[Assert\Length(max: 180)]
        #[OA\Property(type: 'string', format: 'email', example: 'dr.jean@diabcare.com', nullable: true, maxLength: 180)]
        public readonly ?string $email = null,

        #[Assert\Length(min: 8)]
        #[OA\Property(type: 'string', format: 'password', example: 'NewSecurePassword123!', nullable: true, minLength: 8)]
        public readonly ?string $password = null,

        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', example: '+243990000000', nullable: true, maxLength: 50)]
        public readonly ?string $phone = null,

        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', example: 'Dr. Jean Mukendi', nullable: true, maxLength: 150)]
        public readonly ?string $fullName = null,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', example: 'https://diabcare.com/avatars/dr-jean.jpg', nullable: true, maxLength: 500)]
        public readonly ?string $avatarUrl = null,

        #[OA\Property(type: 'string', example: 'MALE', nullable: true, enum: ['MALE', 'FEMALE', 'OTHER', 'UNSPECIFIED'])]
        public readonly ?string $gender = null,

        #[Assert\Length(max: 10)]
        #[OA\Property(type: 'string', example: 'fr', nullable: true, maxLength: 10)]
        public readonly ?string $locale = null,

        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', example: 'ORD-MED-2026-99', nullable: true, maxLength: 100)]
        public readonly ?string $licenseNumber = null,

        #[OA\Property(type: 'string', example: 'CLINICIAN', nullable: true, enum: ['CLINICIAN', 'NUTRITIONIST'])]
        public readonly ?string $professionalType = null,

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
        public readonly ?string $country = null
    ) {}
}
