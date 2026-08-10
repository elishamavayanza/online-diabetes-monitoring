<?php

namespace App\DTO\Request\Treatment;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'MedicationIntakeRequestDTO',
    description: 'Structure de requête pour l’enregistrement d’une prise de médicament'
)]
class MedicationIntakeRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID de l’élément de prescription')]
        public readonly string $prescriptionItemId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T08:00:00Z', description: 'Date et heure de la prise')]
        public readonly \DateTimeImmutable $takenAt,

        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '1.00', description: 'Quantité prise')]
        public readonly string $quantityTaken,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'TAKEN', description: 'Statut de la prise')]
        public readonly mixed $status
    ) {}
}
