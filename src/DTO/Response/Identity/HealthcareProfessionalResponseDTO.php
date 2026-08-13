<?php

namespace App\DTO\Response\Identity;

use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Common\Gender;
use App\Entity\Common\UserStatus;
use App\Entity\Identity\ProfessionalType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'HealthcareProfessionalResponseDTO',
    description: 'Structure des données renvoyées pour un professionnel de santé'
)]
class HealthcareProfessionalResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'email', example: 'dr.jean@diabcare.com', description: 'E-mail')]
        public readonly string $email,

        #[OA\Property(type: 'string', nullable: true, example: '+243990000000', description: 'Téléphone')]
        public readonly ?string $phone,

        #[OA\Property(type: 'string', example: 'Dr. Jean Mukendi', description: 'Nom complet')]
        public readonly string $fullName,

        #[OA\Property(type: 'string', format: 'uri', nullable: true, example: 'https://storage.diabcare.com/avatars/jean.jpg', description: 'Avatar URL')]
        public readonly ?string $avatarUrl,

        #[OA\Property(type: 'string', nullable: true, example: 'MALE', description: 'Genre')]
        public readonly ?Gender $gender,

        #[OA\Property(type: 'string', example: 'fr', description: 'Locale')]
        public readonly string $locale,

        #[OA\Property(type: 'string', nullable: true, example: 'ACTIVE', description: 'Statut du compte')]
        public readonly ?UserStatus $status,

        #[OA\Property(type: 'string', example: 'ORD-MED-2026-99', description: 'Numéro de licence')]
        public readonly string $licenseNumber,

        #[OA\Property(type: 'string', nullable: true, example: 'CLINICIAN', description: 'Type de professionnel')]
        public readonly ?ProfessionalType $professionalType,

        #[OA\Property(type: 'string', nullable: true, example: 'Endocrinologie et Diabétologie', description: 'Spécialité')]
        public readonly ?string $specialty,

        #[OA\Property(type: 'string', format: 'uri', nullable: true, example: 'https://storage.diabcare.com/sigs/jean.png', description: 'Signature URL')]
        public readonly ?string $signatureUrl,

        #[OA\Property(type: 'array', items: new OA\Items(type: 'string'), example: ['ROLE_HEALTHCARE_PROFESSIONAL'], description: 'Rôles utilisateur')]
        public readonly array $roles,

        #[OA\Property(type: 'string', nullable: true, example: '12 Avenue de la Paix', description: 'Rue')]
        public readonly ?string $street,

        #[OA\Property(type: 'string', nullable: true, example: 'Goma', description: 'Ville')]
        public readonly ?string $city,

        #[OA\Property(type: 'string', nullable: true, example: '00243', description: 'Code postal')]
        public readonly ?string $postalCode,

        #[OA\Property(type: 'string', nullable: true, example: 'RDC', description: 'Pays')]
        public readonly ?string $country,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(HealthcareProfessional $professional): self
    {
        $address = $professional->getAddress();

        return new self(
            id: (string) $professional->getId(),
            email: $professional->getEmail(),
            phone: $professional->getPhone(),
            fullName: $professional->getFullName(),
            avatarUrl: $professional->getAvatarUrl(),
            gender: $professional->getGender(),
            locale: $professional->getLocale() ?? 'fr',
            status: $professional->getStatus(),
            licenseNumber: $professional->getLicenseNumber(),
            professionalType: $professional->getProfessionalType(),
            specialty: $professional->getSpecialty(),
            signatureUrl: $professional->getSignatureUrl(),
            roles: $professional->getRoles(),
            street: $address?->getStreet(),
            city: $address?->getCity(),
            postalCode: $address?->getPostalCode(),
            country: $address?->getCountry(),
            createdAt: $professional->getCreatedAt(),
            updatedAt: $professional->getUpdatedAt()
        );
    }
}
