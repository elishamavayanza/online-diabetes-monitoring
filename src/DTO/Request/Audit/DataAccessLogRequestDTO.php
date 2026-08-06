<?php

namespace App\DTO\Request\Audit;

use Symfony\Component\Validator\Constraints as Assert;

class DataAccessLogRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $accessedById,

        #[Assert\NotBlank]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $resourceType,

        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $resourceId,

        #[Assert\Length(max: 5000)]
        public readonly ?string $reason,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $accessedAt
    ) {}
}
