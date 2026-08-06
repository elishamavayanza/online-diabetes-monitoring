<?php

namespace App\DTO\Request\Medical;

use Symfony\Component\Validator\Constraints as Assert;

class PhysicalActivityMeasurementRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public readonly string $activityType,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $durationMinutes,

        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        public readonly ?string $caloriesBurned,

        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        public readonly ?string $minHeartRate,

        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        public readonly ?string $maxHeartRate
    ) {}
}
