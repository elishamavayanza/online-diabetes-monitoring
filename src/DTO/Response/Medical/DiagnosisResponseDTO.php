<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\Diagnosis;

class DiagnosisResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $patientId,
        public readonly string $doctorId,
        public readonly string $conditionName,
        public readonly ?string $description,
        public readonly \DateTimeImmutable $diagnosedAt,
        public readonly string $status,
        public readonly ?string $medicalRecordId,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Diagnosis $diagnosis): self
    {
        return new self(
            id: (string) $diagnosis->getId(),
            patientId: (string) $diagnosis->getPatient()?->getId(),
            doctorId: (string) $diagnosis->getDoctor()?->getId(),
            conditionName: $diagnosis->getConditionName(),
            description: $diagnosis->getDescription(),
            diagnosedAt: $diagnosis->getDiagnosedAt(),
            status: $diagnosis->getStatus(),
            medicalRecordId: $diagnosis->getMedicalRecord()?->getId() ? (string) $diagnosis->getMedicalRecord()->getId() : null,
            createdAt: $diagnosis->getCreatedAt(),
            updatedAt: $diagnosis->getUpdatedAt()
        );
    }
}
