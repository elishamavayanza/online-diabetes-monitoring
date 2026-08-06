<?php

namespace App\DTO\Request\Treatment;

use Symfony\Component\Validator\Constraints as Assert;

class PrescriptionVersionRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $prescriptionId,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $versionNumber,

        #[Assert\Length(max: 5000)]
        public readonly ?string $changesSummary,

        #[Assert\NotNull]
        public readonly array $data,

        #[Assert\NotBlank]
        public readonly string $modifiedById,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $modifiedAt
    ) {}
}
