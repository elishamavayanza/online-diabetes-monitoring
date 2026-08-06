<?php

namespace App\DTO\Request\Identity;

use App\Entity\Common\Gender;
use Symfony\Component\Validator\Constraints as Assert;

class PatientRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 180)]
        public readonly string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        public readonly string $password,

        #[Assert\Length(max: 50)]
        public readonly ?string $phone,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public readonly string $firstName,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public readonly string $lastName,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        public readonly ?string $avatarUrl,

        #[Assert\NotNull]
        public readonly Gender $gender,

        #[Assert\Length(max: 10)]
        public readonly string $locale,

        #[Assert\Date]
        public readonly ?string $dateOfBirth,

        #[Assert\Length(max: 150)]
        public readonly ?string $placeOfBirth,

        #[Assert\Length(max: 10)]
        public readonly ?string $bloodType,

        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        public readonly ?string $heightCm,

        #[Assert\Length(max: 255)]
        public readonly ?string $street,

        #[Assert\Length(max: 100)]
        public readonly ?string $city,

        #[Assert\Length(max: 20)]
        public readonly ?string $postalCode,

        #[Assert\Length(max: 100)]
        public readonly ?string $country
    ) {}
}
