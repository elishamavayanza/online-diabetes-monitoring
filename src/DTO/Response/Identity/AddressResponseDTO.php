<?php

namespace App\DTO\Response\Identity;

use App\Entity\Identity\Address;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'AddressResponseDTO',
    description: 'Structure de réponse pour les données d’adresse'
)]
class AddressResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', nullable: true, example: '12 Avenue de la Paix', description: 'Rue')]
        public readonly ?string $street,

        #[OA\Property(type: 'string', nullable: true, example: 'Goma', description: 'Ville')]
        public readonly ?string $city,

        #[OA\Property(type: 'string', nullable: true, example: '00243', description: 'Code postal')]
        public readonly ?string $postalCode,

        #[OA\Property(type: 'string', nullable: true, example: 'RDC', description: 'Pays')]
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
