<?php

namespace App\DTO\Response\Identity;

use App\Entity\Identity\Patient;
use App\Entity\Common\Gender;
use App\Entity\Common\UserStatus;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'PatientResponseDTO',
    description: 'Structure des données renvoyées pour un patient'
)]
class PatientResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', example: '1', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'email', example: 'patient.marie@gmail.com', description: 'E-mail')]
        public readonly string $email,

        #[OA\Property(type: 'string', nullable: true, example: '+243998887766', description: 'Téléphone')]
        public readonly ?string $phone,

        #[OA\Property(type: 'string', example: 'Marie Zawadi', description: 'Nom complet')]
        public readonly string $fullName,

        #[OA\Property(type: 'string', format: 'uri', nullable: true, example: 'https://storage.diabcare.com/avatars/marie.jpg', description: 'Avatar URL')]
        public readonly ?string $avatarUrl,

        #[OA\Property(type: 'string', nullable: true, example: 'FEMALE', description: 'Genre')]
        public readonly ?Gender $gender,

        #[OA\Property(type: 'string', example: 'fr', description: 'Locale')]
        public readonly string $locale,

        #[OA\Property(type: 'string', nullable: true, example: 'ACTIVE', description: 'Statut du compte')]
        public readonly ?UserStatus $status,

        #[OA\Property(type: 'string', format: 'date', nullable: true, example: '1995-06-15', description: 'Date de naissance')]
        public readonly ?string $dateOfBirth,

        #[OA\Property(type: 'string', nullable: true, example: 'Bukavu', description: 'Lieu de naissance')]
        public readonly ?string $placeOfBirth,

        #[OA\Property(type: 'string', nullable: true, example: 'O+', description: 'Groupe sanguin')]
        public readonly ?string $bloodType,

        #[OA\Property(type: 'string', nullable: true, example: '168.5', description: 'Taille (cm)')]
        public readonly ?string $heightCm,

        #[OA\Property(type: 'array', items: new OA\Items(type: 'string'), example: ['ROLE_PATIENT'], description: 'Rôles utilisateur')]
        public readonly array $roles,

        #[OA\Property(type: 'string', nullable: true, example: '45 Boulevard Kanyamuhanga', description: 'Rue')]
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

    public static function fromEntity(Patient $patient): self
    {
        $address = $patient->getAddress();

        return new self(
            id: (string) $patient->getId(),
            email: $patient->getEmail(),
            phone: $patient->getPhone(),
            fullName: $patient->getFullName(),
            avatarUrl: $patient->getAvatarUrl(),
            gender: $patient->getGender(),
            locale: $patient->getLocale() ?? 'fr',
            status: $patient->getStatus(),
            dateOfBirth: $patient->getDateOfBirth()?->format('Y-m-d'),
            placeOfBirth: $patient->getPlaceOfBirth(),
            bloodType: $patient->getBloodType(),
            heightCm: $patient->getHeightCm(),
            roles: $patient->getRoles(),
            street: $address?->getStreet(),
            city: $address?->getCity(),
            postalCode: $address?->getPostalCode(),
            country: $address?->getCountry(),
            createdAt: $patient->getCreatedAt(),
            updatedAt: $patient->getUpdatedAt()
        );
    }
}
