<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\HealthcareFacility;

class HealthcareFacilityResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $organizationId,
        public readonly string $name,
        public readonly ?array $address,
        public readonly ?string $phone,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(HealthcareFacility $facility): self
    {
        return new self(
            id: (string) $facility->getId(),
            organizationId: (string) $facility->getOrganization()?->getId(),
            name: $facility->getName(),
            address: $facility->getAddress() ? [
                'street' => $facility->getAddress()->getStreet(),
                'city' => $facility->getAddress()->getCity(),
                'state' => $facility->getAddress()->getState(),
                'postalCode' => $facility->getAddress()->getPostalCode(),
                'country' => $facility->getAddress()->getCountry(),
            ] : null,
            phone: $facility->getPhone(),
            createdAt: $facility->getCreatedAt(),
            updatedAt: $facility->getUpdatedAt()
        );
    }
}
