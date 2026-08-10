<?php

namespace App\DTO\Request\Appointment;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'AppointmentReminderRequestDTO',
    description: 'Structure des données pour la programmation d’un rappel de rendez-vous'
)]
class AppointmentReminderRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant unique du rendez-vous associé')]
        public readonly string $appointmentId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'SMS', description: 'Canal de diffusion (SMS, EMAIL, PUSH)')]
        public readonly mixed $channel,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-14T09:00:00Z', description: 'Date et heure prévues pour l’envoi du rappel')]
        public readonly \DateTimeImmutable $scheduledFor,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date d’envoi effective')]
        public readonly ?\DateTimeImmutable $sentAt
    ) {}
}
