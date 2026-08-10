<?php

namespace App\DTO\Request\Medical;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'DiagnosisRequestDTO',
    description: 'Structure de requête pour la création d’un diagnostic'
)]
class DiagnosisRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'Identifiant du patient')]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant du médecin')]
        public readonly string $doctorId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'Diabète de type 2', description: 'Nom de l’affection')]
        public readonly string $conditionName,

        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', maxLength: 5000, nullable: true, example: 'Symptômes observés...', description: 'Description')]
        public readonly ?string $description,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date du diagnostic')]
        public readonly \DateTimeImmutable $diagnosedAt,

        #[Assert\NotBlank]
        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', maxLength: 50, example: 'CONFIRMED', description: 'Statut du diagnostic')]
        public readonly string $status,

        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID du dossier médical')]
        public readonly ?string $medicalRecordId
    ) {}
}
