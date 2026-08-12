<?php

namespace App\DTO\Response\Medical;

use App\Entity\Medical\Diagnosis;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DiagnosisResponseDTO',
    title: 'DiagnosisResponseDTO',
    description: 'Structure de réponse pour un diagnostic médical'
)]
class DiagnosisResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '99cc8877-6655-4433-2211-001122334455', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'ID du médecin')]
        public readonly string $doctorId,

        #[OA\Property(type: 'string', example: 'Diabète de type 2', description: 'Nom de la pathologie')]
        public readonly string $conditionName,

        #[OA\Property(type: 'string', nullable: true, example: 'Symptômes observés...', description: 'Description')]
        public readonly ?string $description,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date du diagnostic')]
        public readonly \DateTimeImmutable $diagnosedAt,

        #[OA\Property(type: 'string', example: 'CONFIRMED', description: 'Statut')]
        public readonly string $status,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID du dossier médical')]
        public readonly ?string $medicalRecordId,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
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
