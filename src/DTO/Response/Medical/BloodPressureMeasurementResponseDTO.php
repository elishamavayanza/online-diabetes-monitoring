<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\BloodPressureMeasurement;

class BloodPressureMeasurementResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $systolic,
        public readonly string $diastolic,
        public readonly ?string $pulse,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(BloodPressureMeasurement $measurement): self
    {
        return new self(
            id: (string) $measurement->getId(),
            systolic: $measurement->getSystolic(),
            diastolic: $measurement->getDiastolic(),
            pulse: $measurement->getPulse(),
            createdAt: $measurement->getCreatedAt(),
            updatedAt: $measurement->getUpdatedAt()
        );
    }
}
