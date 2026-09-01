<?php

namespace App\DTO\Request\Medical;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        #[OA\Property(description: 'Nom de l’examen de laboratoire', type: 'string', example: 'Bilan lipidique complet', maxLength: 150)]
        public readonly string $testName,

        #[Assert\File(
            maxSize: '10M',
            mimeTypes: [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png',
            ],
            mimeTypesMessage: 'Veuillez uploader un fichier PDF, Word ou une image valide (JPEG, PNG).'
        )]
        #[OA\Property(description: 'Fichier du résultat (PDF, Word, Image)', type: 'string', format: 'binary', nullable: true)]
        public readonly ?UploadedFile $file, // Remplacé de fileUrl vers file

        #[Assert\Length(max: 150)]
        #[OA\Property(description: 'Nom du laboratoire', type: 'string', example: 'Laboratoire Central Goma', nullable: true, maxLength: 150)]
        public readonly ?string $labName
    ) {}
}
