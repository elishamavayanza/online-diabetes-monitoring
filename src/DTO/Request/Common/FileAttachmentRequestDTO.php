<?php

namespace App\DTO\Request\Common;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[OA\Schema(
    title: 'FileAttachmentRequestDTO',
    description: 'Structure des données requises pour l’enregistrement d’une pièce jointe'
)]
class FileAttachmentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', maxLength: 255, example: 'resultats_analyse_sang.pdf', description: 'Nom original du fichier')]
        public readonly string $originalName,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', maxLength: 255, example: '68f02a11b24e4_resultats.pdf', description: 'Nom unique du fichier sur le serveur')]
        public readonly string $fileName,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, example: 'application/pdf', description: 'Type MIME')]
        public readonly string $mimeType,

        #[Assert\NotBlank]
        #[Assert\Positive]
        #[OA\Property(type: 'integer', example: 1048576, description: 'Taille en octets')]
        public readonly int $sizeBytes,

        #[Assert\NotBlank]
        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', maxLength: 500, example: 'https://storage.diabcare.com/uploads/2026/08/68f02a11b24e4_resultats.pdf', description: 'URL d’accès au fichier')]
        public readonly string $url,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, example: 'MedicalRecord', description: 'Type de l’entité liée')]
        public readonly string $entityType,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant de l’entité liée')]
        public readonly Uuid $entityId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant de l’utilisateur émetteur')]
        public readonly string $uploadedById
    ) {}
}
