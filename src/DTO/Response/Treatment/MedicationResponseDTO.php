<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\Medication;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'MedicationResponseDTO',
    description: 'Structure de réponse pour un médicament'
)]
class MedicationResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', example: 'Paracétamol 500mg', description: 'Nom du médicament')]
        public readonly string $name,

        #[OA\Property(type: 'string', nullable: true, example: 'ANALGESIC', description: 'Catégorie')]
        public readonly ?string $category,

        #[OA\Property(type: 'string', nullable: true, example: 'Antalgique...', description: 'Description')]
        public readonly ?string $description,

        #[OA\Property(type: 'integer', nullable: true, example: 0, description: 'Niveau d’insuline')]
        public readonly ?int $insulinLevel,

        #[OA\Property(type: 'string', nullable: true, example: 'PharmaLab', description: 'Fabricant')]
        public readonly ?string $manufacturer,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Medication $medication): self
    {
        return new self(
            id: (string) $medication->getId(),
            name: $medication->getName(),
            category: $medication->getCategory()?->value,
            description: $medication->getDescription(),
            insulinLevel: $medication->getInsulinLevel(),
            manufacturer: $medication->getManufacturer(),
            createdAt: $medication->getCreatedAt(),
            updatedAt: $medication->getUpdatedAt()
        );
    }
}
