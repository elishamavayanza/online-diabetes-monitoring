<?php

namespace App\DTO\Request\Healthcare;

use Symfony\Component\Validator\Constraints as Assert;

class CareTeamAssignmentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $patientId,

        #[Assert\NotBlank]
        public readonly string $professionalId,

        #[Assert\NotBlank]
        public readonly string $organizationId,

        #[Assert\NotBlank]
        public readonly mixed $role,

        #[Assert\NotBlank]
        public readonly \DateTimeInterface $startDate,

        public readonly ?\DateTimeInterface $endDate,

        #[Assert\NotNull]
        public readonly bool $active
    ) {}
}
