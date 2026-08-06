<?php

namespace App\DTO\Request\Treatment;

use Symfony\Component\Validator\Constraints as Assert;

class MedicationRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $name,

        #[Assert\NotBlank]
        public readonly mixed $category,

        #[Assert\Length(max: 5000)]
        public readonly ?string $description,

        #[Assert\PositiveOrZero]
        public readonly ?int $insulinLevel,

        #[Assert\Length(max: 150)]
        public readonly ?string $manufacturer
    ) {}
}
