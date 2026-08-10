<?php

namespace App\DTO\Request\Communication;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'MessageAttachmentRequestDTO',
    description: 'Structure des données pour l’ajout d’une pièce jointe à un message'
)]
class MessageAttachmentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '9f881245-33ee-4b11-9a21-4f88e1478c99', description: 'Identifiant du message')]
        public readonly string $messageId,

        #[Assert\NotBlank]
        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', maxLength: 500, example: 'https://storage.diabcare.com/messages/bilan_sanguin.pdf', description: 'URL du fichier')]
        public readonly string $fileUrl,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', maxLength: 255, example: 'bilan_sanguin.pdf', description: 'Nom du fichier')]
        public readonly string $fileName,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'application/pdf', description: 'Type MIME')]
        public readonly string $mimeType,

        #[Assert\NotBlank]
        #[Assert\Positive]
        #[OA\Property(type: 'integer', example: 512000, description: 'Taille en octets')]
        public readonly int $sizeBytes
    ) {}
}
