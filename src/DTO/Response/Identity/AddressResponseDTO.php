<?php

namespace App\DTO\Response\Identity;

use App\Entity\Identity\Address;

class AddressResponseDTO
{
    public function __construct(
        public readonly ?string $street,
        public readonly ?string $city,
        public readonly ?string $postalCode,
        public readonly ?string $country
    ) {}

    public static function fromEntity(?Address $address): ?self
    {
        if ($address === null) {
            return null;
        }

        return new self(
            street: $address->getStreet(),
            city: $address->getCity(),
            postalCode: $address->getPostalCode(),
            country: $address->getCountry()
        );
    }
}
