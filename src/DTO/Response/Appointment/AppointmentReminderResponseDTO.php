<?php

namespace App\DTO\Response\Appointment;

use App\Entity\Appointment\AppointmentReminder;

class AppointmentReminderResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $appointmentId,
        public readonly ?string $channel,
        public readonly \DateTimeImmutable $scheduledFor,
        public readonly ?\DateTimeImmutable $sentAt,
        public readonly \DateTimeImmutable $createdAt,
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
