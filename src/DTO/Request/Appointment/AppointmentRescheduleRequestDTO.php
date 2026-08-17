<?php

namespace App\DTO\Request\Appointment;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'AppointmentRescheduleRequestDTO',
    description: 'Structure des données pour demander le report d\'un rendez-vous'
)]
class AppointmentRescheduleRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(description: 'Nouvelle date et heure souhaitées pour le rendez-vous', type: 'string', format: 'date-time', example: '2026-09-01T10:00:00Z')]
        public readonly \DateTimeImmutable $scheduledAt,
    ) {}
}
