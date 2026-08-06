<?php

namespace App\DTO\Request\Treatment;

use Symfony\Component\Validator\Constraints as Assert;

class PrescriptionItemRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $prescriptionId,

        #[Assert\NotBlank]
        public readonly string $medicationId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public readonly string $dosage,

        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        public readonly string $quantity,

        #[Assert\NotNull]
        public readonly bool $morning,

        #[Assert\NotNull]
        public readonly bool $noon,

        #[Assert\NotNull]
        public readonly bool $evening,

        #[Assert\Length(max: 5000)]
        public readonly ?string $instructions
    ) {}
}
