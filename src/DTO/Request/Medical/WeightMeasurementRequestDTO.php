<?php

namespace App\DTO\Request\Medical;

use Symfony\Component\Validator\Constraints as Assert;

class WeightMeasurementRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        public readonly string $valueKg,

        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        public readonly ?string $heightCm
    ) {}
}
