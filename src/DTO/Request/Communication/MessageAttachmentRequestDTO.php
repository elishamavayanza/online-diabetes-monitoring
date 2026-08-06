<?php

namespace App\DTO\Request\Communication;

use Symfony\Component\Validator\Constraints as Assert;

class MessageAttachmentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $messageId,

        #[Assert\NotBlank]
        #[Assert\Url]
        #[Assert\Length(max: 500)]
        public readonly string $fileUrl,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $fileName,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $mimeType,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $sizeBytes
    ) {}
}
