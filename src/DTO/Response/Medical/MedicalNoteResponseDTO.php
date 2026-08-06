<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\MedicalNote;

class MedicalNoteResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $medicalRecordId,
        public readonly string $authorId,
        public readonly string $content,
        public readonly \DateTimeImmutable $notedAt,
        public readonly \DateTimeImmutable $createdAt,
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
