<?php

namespace App\DTO\Request\Medical;

use Symfony\Component\Validator\Constraints as Assert;

class MedicalRecordRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $patientId,

        #[Assert\NotBlank]
        public readonly string $organizationId,

        #[Assert\NotBlank]
        public readonly mixed $status,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $openedAt,

        public readonly ?\DateTimeImmutable $closedAt
    ) {}
}
