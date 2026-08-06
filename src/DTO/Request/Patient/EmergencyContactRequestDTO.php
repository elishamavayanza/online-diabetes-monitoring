<?php

namespace App\DTO\Request\Patient;

use Symfony\Component\Validator\Constraints as Assert;

class EmergencyContactRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $fullName,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public readonly string $relationship,

        #[Assert\NotBlank]
        #[Assert\Length(max: 50)]
        public readonly string $phone,

        #[Assert\Email]
        #[Assert\Length(max: 180)]
        public readonly ?string $email
    ) {}
}
