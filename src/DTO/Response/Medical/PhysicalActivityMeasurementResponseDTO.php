<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\PhysicalActivityMeasurement;

class PhysicalActivityMeasurementResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $activityType,
        public readonly int $durationMinutes,
        public readonly ?string $caloriesBurned,
        public readonly ?string $minHeartRate,
        public readonly ?string $maxHeartRate,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(PhysicalActivityMeasurement $measurement): self
    {
        return new self(
            id: (string) $measurement->getId(),
            activityType: $measurement->getActivityType(),
            durationMinutes: $measurement->getDurationMinutes(),
            caloriesBurned: $measurement->getCaloriesBurned(),
            minHeartRate: $measurement->getMinHeartRate(),
            maxHeartRate: $measurement->getMaxHeartRate(),
            createdAt: $measurement->getCreatedAt(),
            updatedAt: $measurement->getUpdatedAt()
        );
    }
}
