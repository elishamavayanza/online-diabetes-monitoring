<?php

namespace App\DTO\Request\Appointment;

use Symfony\Component\Validator\Constraints as Assert;

class AppointmentReminderRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $appointmentId,

        #[Assert\NotBlank]
        public readonly mixed $channel,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $scheduledFor,

        public readonly ?\DateTimeImmutable $sentAt
    ) {}
}
