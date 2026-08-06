<?php

namespace App\DTO\Request\Common;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

class FileAttachmentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $originalName,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $fileName,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public readonly string $mimeType,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $sizeBytes,

        #[Assert\NotBlank]
        #[Assert\Url]
        #[Assert\Length(max: 500)]
        public readonly string $url,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public readonly string $entityType,

        #[Assert\NotBlank]
        public readonly Uuid $entityId,

        #[Assert\NotBlank]
        public readonly string $uploadedById
    ) {}
}
