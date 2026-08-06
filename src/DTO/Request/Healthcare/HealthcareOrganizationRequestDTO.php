<?php

namespace App\DTO\Request\Healthcare;

use Symfony\Component\Validator\Constraints as Assert;

class HealthcareOrganizationRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $name,

        #[Assert\Length(max: 50)]
        public readonly ?string $shortName,

        #[Assert\NotBlank]
        public readonly mixed $type,

        #[Assert\Email]
        #[Assert\Length(max: 180)]
        public readonly ?string $email,

        #[Assert\Length(max: 50)]
        public readonly ?string $phone,

        #[Assert\Url]
        #[Assert\Length(max: 255)]
        public readonly ?string $website,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        public readonly ?string $logoUrl,

        public readonly ?array $address,

        #[Assert\NotNull]
        public readonly bool $active
    ) {}
}
