<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\HealthcareOrganization;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'HealthcareOrganizationResponseDTO',
    description: 'Structure des données renvoyées pour une organisation de santé'
)]
class HealthcareOrganizationResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant unique de l’organisation')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: 'DiabCare Health Group', description: 'Nom')]
        public readonly string $name,

        #[OA\Property(type: 'string', nullable: true, example: 'DHG', description: 'Nom court')]
        public readonly ?string $shortName,

        #[OA\Property(type: 'string', nullable: true, example: 'HOSPITAL_NETWORK', description: 'Type')]
        public readonly ?string $type,

        #[OA\Property(type: 'string', format: 'email', nullable: true, example: 'contact@diabcare.com', description: 'E-mail')]
        public readonly ?string $email,

        #[OA\Property(type: 'string', nullable: true, example: '+243990000000', description: 'Téléphone')]
        public readonly ?string $phone,

        #[OA\Property(type: 'string', format: 'uri', nullable: true, example: 'https://www.diabcare.com', description: 'Site Web')]
        public readonly ?string $website,

        #[OA\Property(type: 'string', format: 'uri', nullable: true, example: 'https://storage.diabcare.com/logos/dhg.png', description: 'Logo URL')]
        public readonly ?string $logoUrl,

        #[OA\Property(type: 'object', nullable: true, description: 'Adresse structurée')]
        public readonly ?array $address,

        #[OA\Property(type: 'boolean', example: true, description: 'Statut actif')]
        public readonly bool $active,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(HealthcareOrganization $organization): self
    {
        return new self(
            id: (string) $organization->getId(),
            name: $organization->getName(),
            shortName: $organization->getShortName(),
            type: $organization->getType()?->value,
            email: $organization->getEmail(),
            phone: $organization->getPhone(),
            website: $organization->getWebsite(),
            logoUrl: $organization->getLogoUrl(),
            address: $organization->getAddress() ? [
                'street' => $organization->getAddress()->getStreet(),
                'city' => $organization->getAddress()->getCity(),
                'state' => $organization->getAddress()->getState(),
                'postalCode' => $organization->getAddress()->getPostalCode(),
                'country' => $organization->getAddress()->getCountry(),
            ] : null,
            active: $organization->isActive(),
            createdAt: $organization->getCreatedAt(),
            updatedAt: $organization->getUpdatedAt()
        );
    }
}
