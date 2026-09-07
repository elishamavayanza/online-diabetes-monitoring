<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\Medication;
use App\Entity\Treatment\MedicationClass;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MedicationResponseDTO',
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

        #[OA\Property(type: 'string', nullable: true, example: 'INSULIN', description: 'Classe du médicament (INSULIN ou GENERAL)')]
        public readonly ?string $category,

        #[OA\Property(type: 'string', nullable: true, example: 'TABLET', description: 'Forme galénique (TABLET ou LIQUID, si classe GENERAL)')]
        public readonly ?string $form,

        #[OA\Property(type: 'string', nullable: true, example: 'Antidiabétique...', description: 'Description')]
        public readonly ?string $description,

        #[OA\Property(type: 'string', nullable: true, example: 'PharmaLab', description: 'Fabricant')]
        public readonly ?string $manufacturer,

        #[OA\Property(type: 'boolean', example: true, description: 'Médicament actif dans le référentiel')]
        public readonly bool $active,

        #[OA\Property(type: 'string', nullable: true, example: 'LONG_ACTING', description: 'Type d’insuline (si classe INSULIN)')]
        public readonly ?string $insulinType,

        #[OA\Property(type: 'string', nullable: true, example: 'U-100', description: 'Concentration de l’insuline (si classe INSULIN)')]
        public readonly ?string $concentration,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Medication $medication): self
    {
        $insulin = $medication->getInsulins()->first();

        return new self(
            id: (string) $medication->getId(),
            name: $medication->getName(),
            category: $medication->getCategory()?->value,
            form: $medication->getCategory() === MedicationClass::GENERAL ? $medication->getForm()?->value : null,
            description: $medication->getDescription(),
            manufacturer: $medication->getManufacturer(),
            active: $medication->isActive(),
            insulinType: $insulin ? $insulin->getInsulinType()?->value : null,
            concentration: $insulin ? $insulin->getConcentration() : null,
            createdAt: $medication->getCreatedAt(),
            updatedAt: $medication->getUpdatedAt()
        );
    }
}
