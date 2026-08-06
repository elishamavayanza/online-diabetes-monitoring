<?php

namespace App\DTO\Request\Treatment;

use Symfony\Component\Validator\Constraints as Assert;

class MedicationIntakeRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $prescriptionItemId,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $takenAt,

        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        public readonly string $quantityTaken,

        #[Assert\NotBlank]
        public readonly mixed $status
    ) {}
}
