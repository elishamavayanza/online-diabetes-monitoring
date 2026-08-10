<?php

namespace App\DTO\Request\Medical;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'LaboratoryResultRequestDTO',
    description: 'Structure de requête pour l’ajout d’un résultat de laboratoire'
)]
class LaboratoryResultRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'Bilan lipidique complet', description: 'Nom de l’examen de laboratoire')]
        public readonly string $testName,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://storage.diabcare.com/labs/result-123.pdf', description: 'URL du fichier')]
        public readonly ?string $fileUrl,

        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, nullable: true, example: 'Laboratoire Central Goma', description: 'Nom du laboratoire')]
        public readonly ?string $labName
    ) {}
}
