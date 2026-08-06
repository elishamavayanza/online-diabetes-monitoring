<?php

namespace App\DTO\Request\Treatment;

use Symfony\Component\Validator\Constraints as Assert;

class PrescriptionRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $patientId,

        #[Assert\NotBlank]
        public readonly string $prescriberId,

        #[Assert\NotBlank]
        public readonly string $organizationId,

        #[Assert\NotBlank]
        public readonly \DateTimeInterface $startDate,

        public readonly ?\DateTimeInterface $endDate,

        #[Assert\NotBlank]
        public readonly mixed $status,

        #[Assert\Length(max: 5000)]
        public readonly ?string $notes,

        public readonly ?\DateTimeImmutable $validatedAt,

        public readonly ?string $validatedById
    ) {}
}
