<?php

namespace App\DTO\Response\Identity;

use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Common\Gender;
use App\Entity\Common\UserStatus;
use App\Entity\Identity\ProfessionalType;

class HealthcareProfessionalResponseDTO
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
        public readonly string $licenseNumber,
        public readonly ?ProfessionalType $professionalType,
        public readonly ?string $specialty,
        public readonly ?string $signatureUrl,
        public readonly array $roles,
        public readonly ?string $street,
        public readonly ?string $city,
        public readonly ?string $postalCode,
        public readonly ?string $country,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(HealthcareProfessional $professional): self
    {
        $address = $professional->getAddress();

        return new self(
            id: (string) $professional->getId(),
            email: $professional->getEmail(),
            phone: $professional->getPhone(),
            firstName: $professional->getFirstName(),
            lastName: $professional->getLastName(),
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
