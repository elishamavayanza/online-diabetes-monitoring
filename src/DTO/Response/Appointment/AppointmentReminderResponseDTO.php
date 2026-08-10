<?php

namespace App\DTO\Response\Appointment;

use App\Entity\Appointment\AppointmentReminder;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'AppointmentReminderResponseDTO',
    description: 'Structure des données renvoyées pour un rappel de rendez-vous'
)]
class AppointmentReminderResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '8f124455-88aa-4c12-9811-3e449102b111', description: 'Identifiant unique du rappel')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant unique du rendez-vous')]
        public readonly string $appointmentId,

        #[OA\Property(type: 'string', nullable: true, example: 'SMS', description: 'Canal de notification')]
        public readonly ?string $channel,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-14T09:00:00Z', description: 'Date prévue d’envoi')]
        public readonly \DateTimeImmutable $scheduledFor,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date d’envoi effectif')]
        public readonly ?\DateTimeImmutable $sentAt,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T08:00:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(AppointmentReminder $reminder): self
    {
        return new self(
            id: (string) $reminder->getId(),
            appointmentId: (string) $reminder->getAppointment()?->getId(),
            channel: $reminder->getChannel()?->value,
            scheduledFor: $reminder->getScheduledFor(),
            sentAt: $reminder->getSentAt(),
            createdAt: $reminder->getCreatedAt(),
            updatedAt: $reminder->getUpdatedAt()
        );
    }
}
