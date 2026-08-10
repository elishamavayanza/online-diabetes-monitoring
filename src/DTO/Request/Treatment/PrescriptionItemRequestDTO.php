<?php

namespace App\DTO\Request\Treatment;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'PrescriptionItemRequestDTO',
    description: 'Structure de requête pour l’ajout d’un élément de prescription'
)]
class PrescriptionItemRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '99001122-3344-5566-7788-99aabbccddeev', description: 'ID de la prescription')]
        public readonly string $prescriptionId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID du médicament')]
        public readonly string $medicationId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, example: '1 comprimé', description: 'Posologie')]
        public readonly string $dosage,

        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', example: '1.00', description: 'Quantité')]
        public readonly string $quantity,

        #[Assert\NotNull]
        #[OA\Property(type: 'boolean', example: true, description: 'Prise le matin')]
        public readonly bool $morning,

        #[Assert\NotNull]
        #[OA\Property(type: 'boolean', example: false, description: 'Prise le midi')]
        public readonly bool $noon,

        #[Assert\NotNull]
        #[OA\Property(type: 'boolean', example: true, description: 'Prise le soir')]
        public readonly bool $evening,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Instructions...', description: 'Instructions')]
        public readonly ?string $instructions
    ) {}
}
