<?php

namespace App\DTO\Request\Patient;

use Symfony\Component\Validator\Constraints as Assert;

class AllergyRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $name,

        #[Assert\NotBlank]
        public readonly mixed $severity,

        #[Assert\Length(max: 5000)]
        public readonly ?string $reaction,

        #[Assert\Length(max: 5000)]
        public readonly ?string $notes,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $diagnosedAt
    ) {}
}
