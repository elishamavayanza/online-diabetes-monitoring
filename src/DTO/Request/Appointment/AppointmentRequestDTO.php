<?php

namespace App\DTO\Request\Appointment;

use Symfony\Component\Validator\Constraints as Assert;

class AppointmentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $patientId,

        #[Assert\NotBlank]
        public readonly string $professionalId,

        #[Assert\NotBlank]
        public readonly string $organizationId,

        public readonly ?string $facilityId,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $scheduledAt,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $durationMinutes,

        #[Assert\NotBlank]
        public readonly mixed $status,

        #[Assert\Length(max: 255)]
        public readonly ?string $reason,

        #[Assert\Length(max: 5000)]
        public readonly ?string $notes
    ) {}
}
