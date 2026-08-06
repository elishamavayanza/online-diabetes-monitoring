<?php

namespace App\DTO\Request\Healthcare;

use Symfony\Component\Validator\Constraints as Assert;

class DepartmentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $facilityId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $name,

        #[Assert\Length(max: 150)]
        public readonly ?string $specialty
    ) {}
}
