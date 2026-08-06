<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\BloodGlucoseMeasurement;

class BloodGlucoseMeasurementResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $value,
        public readonly ?string $unit,
        public readonly ?string $context,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(BloodGlucoseMeasurement $measurement): self
    {
        return new self(
            id: (string) $measurement->getId(),
            value: $measurement->getValue(),
            unit: $measurement->getUnit()?->value,
            context: $measurement->getContext()?->value,
            createdAt: $measurement->getCreatedAt(),
            updatedAt: $measurement->getUpdatedAt()
        );
    }
}
