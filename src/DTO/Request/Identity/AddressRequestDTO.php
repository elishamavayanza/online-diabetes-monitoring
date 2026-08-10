<?php

namespace App\DTO\Request\Identity;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'AddressRequestDTO',
    description: 'Structure de requête pour les données d’adresse'
)]
class AddressRequestDTO
{
    public function __construct(
        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', maxLength: 255, nullable: true, example: '12 Avenue de la Paix', description: 'Nom de la rue')]
        public readonly ?string $street,

        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, nullable: true, example: 'Goma', description: 'Ville')]
        public readonly ?string $city,

        #[Assert\Length(max: 20)]
        #[OA\Property(type: 'string', maxLength: 20, nullable: true, example: '00243', description: 'Code postal')]
        public readonly ?string $postalCode,

        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, nullable: true, example: 'RDC', description: 'Pays')]
        public readonly ?string $country
    ) {}
}
