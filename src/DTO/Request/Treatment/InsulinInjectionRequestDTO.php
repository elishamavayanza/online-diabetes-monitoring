<?php

namespace App\DTO\Request\Treatment;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'InsulinInjectionRequestDTO',
    description: 'Structure de requête pour l’enregistrement d’une injection d’insuline'
)]
class InsulinInjectionRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID de l’élément de prescription')]
        public readonly string $prescriptionItemId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '44cc1245-12f4-4b53-8811-7a6543210999', description: 'ID de l’insuline injectée')]
        public readonly string $insulinId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T08:00:00Z', description: 'Date et heure de l’injection')]
        public readonly \DateTimeImmutable $injectedAt,

        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '12.00', description: 'Dose d’insuline en unités')]
        public readonly string $doseUnits,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'ABDOMEN', description: 'Site d’injection')]
        public readonly mixed $injectionSite,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'TAKEN', description: 'Statut de l’injection')]
        public readonly mixed $status,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Injection effectuée après le repas.', description: 'Notes complémentaires')]
        public readonly ?string $notes
    ) {}
}