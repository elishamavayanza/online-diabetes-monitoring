<?php

namespace App\DTO\Request\Medical;

use Symfony\Component\Validator\Constraints as Assert;

class MedicalNoteRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $medicalRecordId,

        #[Assert\NotBlank]
        public readonly string $authorId,

        #[Assert\NotBlank]
        public readonly string $content,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $notedAt
    ) {}
}
