<?php

namespace App\DTO\Request\Patient;

use Symfony\Component\Validator\Constraints as Assert;

class MedicalConsentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $patientId,

        public readonly ?string $organizationId,

        #[Assert\NotBlank]
        public readonly mixed $consentType,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $grantedAt,

        public readonly ?\DateTimeImmutable $revokedAt,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        public readonly ?string $documentUrl
    ) {}
}
