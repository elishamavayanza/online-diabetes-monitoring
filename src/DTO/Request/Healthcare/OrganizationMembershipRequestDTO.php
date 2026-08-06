<?php

namespace App\DTO\Request\Healthcare;

use Symfony\Component\Validator\Constraints as Assert;

class OrganizationMembershipRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $userId,

        #[Assert\NotBlank]
        public readonly string $organizationId,

        public readonly ?string $facilityId,

        public readonly ?string $departmentId,

        #[Assert\NotBlank]
        public readonly \DateTimeInterface $startDate,

        public readonly ?\DateTimeInterface $endDate,

        #[Assert\NotBlank]
        public readonly mixed $status
    ) {}
}
