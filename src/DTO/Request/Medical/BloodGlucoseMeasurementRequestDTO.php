<?php

namespace App\DTO\Request\Medical;

use Symfony\Component\Validator\Constraints as Assert;

class BloodGlucoseMeasurementRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        public readonly string $value,

        #[Assert\NotBlank]
        public readonly mixed $unit,

        #[Assert\NotBlank]
        public readonly mixed $context
    ) {}
}
