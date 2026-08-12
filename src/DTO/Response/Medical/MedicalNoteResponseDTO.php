<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\MedicalNote;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MedicalNoteResponseDTO',
    title: 'MedicalNoteResponseDTO',
    description: 'Structure de réponse pour une note médicale'
)]
class MedicalNoteResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '22bb1100-9988-7766-5544-33221100ffee', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID du dossier médical')]
        public readonly string $medicalRecordId,

        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'ID de l’auteur')]
        public readonly string $authorId,

        #[OA\Property(type: 'string', example: 'Patient stable...', description: 'Contenu')]
        public readonly string $content,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date de la note')]
        public readonly \DateTimeImmutable $notedAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(MedicalNote $note): self
    {
        return new self(
            id: (string) $note->getId(),
            medicalRecordId: (string) $note->getMedicalRecord()?->getId(),
            authorId: (string) $note->getAuthor()?->getId(),
            content: $note->getContent(),
            notedAt: $note->getNotedAt(),
            createdAt: $note->getCreatedAt(),
            updatedAt: $note->getUpdatedAt()
        );
    }
}
