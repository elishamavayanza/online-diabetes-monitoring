<?php

namespace App\DTO\Request\Medical;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'MedicalNoteRequestDTO',
    description: 'Structure de requête pour la création d’une note médicale'
)]
class MedicalNoteRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'Identifiant du dossier médical')]
        public readonly string $medicalRecordId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant de l’auteur')]
        public readonly string $authorId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'Patient stable...', description: 'Contenu de la note')]
        public readonly string $content,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date et heure de la note')]
        public readonly \DateTimeImmutable $notedAt
    ) {}
}
