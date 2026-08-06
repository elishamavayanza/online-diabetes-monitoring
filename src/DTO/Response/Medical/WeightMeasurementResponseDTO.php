<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\WeightMeasurement;

class WeightMeasurementResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $valueKg,
        public readonly ?string $heightCm,
        public readonly ?string $bmi,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(WeightMeasurement $measurement): self
    {
        return new self(
            id: (string) $measurement->getId(),
            valueKg: $measurement->getValueKg(),
            heightCm: $measurement->getHeightCm(),
            bmi: $measurement->getBmi(),
            createdAt: $measurement->getCreatedAt(),
            updatedAt: $measurement->getUpdatedAt()
        );
    }
}
