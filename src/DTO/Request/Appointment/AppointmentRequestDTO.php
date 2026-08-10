<?php

namespace App\DTO\Request\Appointment;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'AppointmentRequestDTO',
    description: 'Structure des données requises pour la création d’un rendez-vous médical'
)]
class AppointmentRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '4a613328-98e3-4d64-8898-0c06a3861c8f', description: 'Identifiant unique du patient')]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant unique du professionnel de santé')]
        public readonly string $professionalId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '1c552144-88ef-4a92-b4c4-7893a12b4e55', description: 'Identifiant unique de l’organisation de santé')]
        public readonly string $organizationId,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '9f881245-33ee-4b11-9a21-4f88e1478c99', description: 'Identifiant de l’établissement (optionnel)')]
        public readonly ?string $facilityId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-15T10:30:00Z', description: 'Date et heure prévues du rendez-vous')]
        public readonly \DateTimeImmutable $scheduledAt,

        #[Assert\NotBlank]
        #[Assert\Positive]
        #[OA\Property(type: 'integer', example: 30, description: 'Durée du rendez-vous en minutes')]
        public readonly int $durationMinutes,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'SCHEDULED', description: 'Statut initial du rendez-vous')]
        public readonly mixed $status,

        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', maxLength: 255, nullable: true, example: 'Contrôle trimestriel du taux de glycémie', description: 'Motif du rendez-vous')]
        public readonly ?string $reason,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Le patient apporte ses derniers résultats d’analyse sanguine.', description: 'Notes cliniques ou administratives')]
        public readonly ?string $notes
    ) {}
}
