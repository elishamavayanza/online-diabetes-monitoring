<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\HealthcareOrganization;

class HealthcareOrganizationResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $shortName,
        public readonly ?string $type,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $website,
        public readonly ?string $logoUrl,
        public readonly ?array $address,
        public readonly bool $active,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(HealthcareOrganization $organization): self
    {
        return new self(
            id: (string) $organization->getId(),
            name: $organization->getName(),
            shortName: $organization->getShortName(),
            type: $organization->getType()?->value,
            email: $organization->getEmail(),
            phone: $organization->getPhone(),
            website: $organization->getWebsite(),
            logoUrl: $organization->getLogoUrl(),
            address: $organization->getAddress() ? [
                'street' => $organization->getAddress()->getStreet(),
                'city' => $organization->getAddress()->getCity(),
                'state' => $organization->getAddress()->getState(),
                'postalCode' => $organization->getAddress()->getPostalCode(),
                'country' => $organization->getAddress()->getCountry(),
            ] : null,
            active: $organization->isActive(),
            createdAt: $organization->getCreatedAt(),
            updatedAt: $organization->getUpdatedAt()
        );
    }
}
