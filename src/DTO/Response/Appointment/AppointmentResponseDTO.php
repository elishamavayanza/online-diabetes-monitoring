<?php

namespace App\DTO\Response\Appointment;

use App\Entity\Appointment\Appointment;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'AppointmentResponseDTO',
    description: 'Structure des données renvoyées après la création ou la récupération d’un rendez-vous'
)]
class AppointmentResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant unique du rendez-vous')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '4a613328-98e3-4d64-8898-0c06a3861c8f', description: 'Identifiant unique du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant unique du professionnel de santé')]
        public readonly string $professionalId,

        #[OA\Property(type: 'string', format: 'uuid', example: '1c552144-88ef-4a92-b4c4-7893a12b4e55', description: 'Identifiant unique de l’organisation de santé')]
        public readonly string $organizationId,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '9f881245-33ee-4b11-9a21-4f88e1478c99', description: 'Identifiant de l’établissement')]
        public readonly ?string $facilityId,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-15T10:30:00Z', description: 'Date et heure prévues du rendez-vous')]
        public readonly \DateTimeImmutable $scheduledAt,

        #[OA\Property(type: 'integer', example: 30, description: 'Durée en minutes')]
        public readonly int $durationMinutes,

        #[OA\Property(type: 'string', nullable: true, example: 'SCHEDULED', description: 'Statut du rendez-vous')]
        public readonly ?string $status,

        #[OA\Property(type: 'string', nullable: true, example: 'Contrôle trimestriel du taux de glycémie', description: 'Motif')]
        public readonly ?string $reason,

        #[OA\Property(type: 'string', nullable: true, example: 'Le patient apporte ses derniers résultats d’analyse sanguine.', description: 'Notes')]
        public readonly ?string $notes,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T08:00:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de dernière modification')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Appointment $appointment): self
    {
        return new self(
            id: (string) $appointment->getId(),
            patientId: (string) $appointment->getPatient()?->getId(),
            professionalId: (string) $appointment->getProfessional()?->getId(),
            organizationId: (string) $appointment->getOrganization()?->getId(),
            facilityId: $appointment->getFacility()?->getId() ? (string) $appointment->getFacility()->getId() : null,
            scheduledAt: $appointment->getScheduledAt(),
            durationMinutes: $appointment->getDurationMinutes(),
            status: $appointment->getStatus()?->value,
            reason: $appointment->getReason(),
            notes: $appointment->getNotes(),
            createdAt: $appointment->getCreatedAt(),
            updatedAt: $appointment->getUpdatedAt()
        );
    }
}
