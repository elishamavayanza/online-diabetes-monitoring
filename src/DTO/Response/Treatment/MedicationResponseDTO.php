<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\Medication;

class MedicationResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $category,
        public readonly ?string $description,
        public readonly ?int $insulinLevel,
        public readonly ?string $manufacturer,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Medication $medication): self
    {
        return new self(
            id: (string) $medication->getId(),
            name: $medication->getName(),
            category: $medication->getCategory()?->value,
            description: $medication->getDescription(),
            insulinLevel: $medication->getInsulinLevel(),
            manufacturer: $medication->getManufacturer(),
            createdAt: $medication->getCreatedAt(),
            updatedAt: $medication->getUpdatedAt()
        );
    }
}
