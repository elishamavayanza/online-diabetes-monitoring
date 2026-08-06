<?php

namespace App\DTO\Request\Healthcare;

use Symfony\Component\Validator\Constraints as Assert;

class HealthcareFacilityRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $organizationId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $name,

        public readonly ?array $address,

        #[Assert\Length(max: 50)]
        public readonly ?string $phone
    ) {}
}
