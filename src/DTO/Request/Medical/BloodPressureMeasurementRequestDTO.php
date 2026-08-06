<?php

namespace App\DTO\Request\Medical;

use Symfony\Component\Validator\Constraints as Assert;

class BloodPressureMeasurementRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        public readonly string $systolic,

        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        public readonly string $diastolic,

        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        public readonly ?string $pulse
    ) {}
}
