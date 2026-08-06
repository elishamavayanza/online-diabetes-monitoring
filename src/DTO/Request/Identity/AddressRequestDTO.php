<?php

namespace App\DTO\Request\Identity;

use App\Entity\Common\Gender;
use App\Entity\Common\UserStatus;
use Symfony\Component\Validator\Constraints as Assert;

class AddressRequestDTO
{
    public function __construct(
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
