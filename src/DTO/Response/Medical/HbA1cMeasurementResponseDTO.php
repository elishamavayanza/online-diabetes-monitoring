<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\HbA1cMeasurement;

class HbA1cMeasurementResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $valuePercent,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(HbA1cMeasurement $measurement): self
    {
        return new self(
            id: (string) $measurement->getId(),
            valuePercent: $measurement->getValuePercent(),
            createdAt: $measurement->getCreatedAt(),
            updatedAt: $measurement->getUpdatedAt()
        );
    }
}
