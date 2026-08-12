<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\HealthcareFacility;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'HealthcareFacilityResponseDTO',
    title: 'HealthcareFacilityResponseDTO',
    description: 'Structure des données renvoyées pour un établissement de santé'
)]
class HealthcareFacilityResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '11bb22cc-33ee-4ff1-8811-9a8877665544', description: 'Identifiant unique de l’établissement')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant de l’organisation')]
        public readonly string $organizationId,

        #[OA\Property(type: 'string', example: 'Hôpital Général de Référence', description: 'Nom')]
        public readonly string $name,

        #[OA\Property(type: 'object', nullable: true, description: 'Adresse structurée')]
        public readonly ?array $address,

        #[OA\Property(type: 'string', nullable: true, example: '+243990000000', description: 'Téléphone')]
        public readonly ?string $phone,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(HealthcareFacility $facility): self
    {
        return new self(
            id: (string) $facility->getId(),
            organizationId: (string) $facility->getOrganization()?->getId(),
            name: $facility->getName(),
            address: $facility->getAddress() ? [
                'street' => $facility->getAddress()->getStreet(),
                'city' => $facility->getAddress()->getCity(),
                'state' => $facility->getAddress()->getState(),
                'postalCode' => $facility->getAddress()->getPostalCode(),
                'country' => $facility->getAddress()->getCountry(),
            ] : null,
            phone: $facility->getPhone(),
            createdAt: $facility->getCreatedAt(),
            updatedAt: $facility->getUpdatedAt()
        );
    }
}
