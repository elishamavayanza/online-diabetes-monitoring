<?php

namespace App\DTO\Response\Appointment;

use App\Entity\Appointment\Appointment;

class AppointmentResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $patientId,
        public readonly string $professionalId,
        public readonly string $organizationId,
        public readonly ?string $facilityId,
        public readonly \DateTimeImmutable $scheduledAt,
        public readonly int $durationMinutes,
        public readonly ?string $status,
        public readonly ?string $reason,
        public readonly ?string $notes,
        public readonly \DateTimeImmutable $createdAt,
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
