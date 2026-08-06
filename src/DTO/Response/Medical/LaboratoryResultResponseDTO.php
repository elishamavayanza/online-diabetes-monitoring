<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\LaboratoryResult;

class LaboratoryResultResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $testName,
        public readonly ?string $fileUrl,
        public readonly ?string $labName,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(LaboratoryResult $result): self
    {
        return new self(
            id: (string) $result->getId(),
            testName: $result->getTestName(),
            fileUrl: $result->getFileUrl(),
            labName: $result->getLabName(),
            createdAt: $result->getCreatedAt(),
            updatedAt: $result->getUpdatedAt()
        );
    }
}
