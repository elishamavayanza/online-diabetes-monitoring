<?php

namespace App\DTO\Response\Identity;

use App\Entity\Identity\Patient;
use App\Entity\Common\Gender;
use App\Entity\Common\UserStatus;

class PatientResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?string $avatarUrl,
        public readonly ?Gender $gender,
        public readonly string $locale,
        public readonly ?UserStatus $status,
        public readonly ?string $dateOfBirth,
        public readonly ?string $placeOfBirth,
        public readonly ?string $bloodType,
        public readonly ?string $heightCm,
        public readonly array $roles,
        public readonly ?string $street,
        public readonly ?string $city,
        public readonly ?string $postalCode,
        public readonly ?string $country,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Patient $patient): self
    {
        $address = $patient->getAddress();

        return new self(
            id: (string) $patient->getId(),
            email: $patient->getEmail(),
            phone: $patient->getPhone(),
            firstName: $patient->getFirstName(),
            lastName: $patient->getLastName(),
            avatarUrl: $patient->getAvatarUrl(),
            gender: $patient->getGender(),
            locale: $patient->getLocale() ?? 'fr',
            status: $patient->getStatus(),
            dateOfBirth: $patient->getDateOfBirth()?->format('YYYY-mm-dd'),
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
