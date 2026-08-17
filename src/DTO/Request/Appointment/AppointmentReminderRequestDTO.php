<?php

namespace App\DTO\Request\Appointment;

use App\Entity\Appointment\ReminderChannel;
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
        #[Assert\Positive]
        #[OA\Property(description: 'Identifiant du rendez-vous associé', type: 'integer', format: 'int64', example: 1)]
        public readonly int $appointmentId,

        #[Assert\NotBlank]
        #[Assert\Choice(callback: [ReminderChannel::class, 'values'])]
        #[OA\Property(description: 'Canal de diffusion', type: 'string', example: 'SMS', enum: ['EMAIL', 'SMS', 'PUSH', 'IN_APP'])]
        public readonly string $channel,

        #[Assert\NotBlank]
        #[OA\Property(description: 'Date et heure prévues pour l’envoi du rappel', type: 'string', format: 'date-time', example: '2026-08-14T09:00:00Z')]
        public readonly \DateTimeImmutable $scheduledFor,

        #[OA\Property(description: 'Date d’envoi effective', type: 'string', format: 'date-time', example: null, nullable: true)]
        public readonly ?\DateTimeImmutable $sentAt
    ) {}
}
