<?php

namespace App\DTO\Request\Treatment;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'PrescriptionVersionRequestDTO',
    description: 'Structure de requête pour la création d’une version de prescription'
)]
class PrescriptionVersionRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '99001122-3344-5566-7788-99aabbccddeev', description: 'ID de la prescription')]
        public readonly string $prescriptionId,

        #[Assert\NotBlank]
        #[Assert\Positive]
        #[OA\Property(type: 'integer', minimum: 1, example: 2, description: 'Numéro de version')]
        public readonly int $versionNumber,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Résumé des modifications...', description: 'Résumé des modifications')]
        public readonly ?string $changesSummary,

        #[Assert\NotNull]
        #[OA\Property(type: 'object', example: ['key' => 'value'], description: 'Données de la version')]
        public readonly array $data,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID de l’auteur')]
        public readonly string $modifiedById,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:00:00Z', description: 'Date de modification')]
        public readonly \DateTimeImmutable $modifiedAt
    ) {}
}
