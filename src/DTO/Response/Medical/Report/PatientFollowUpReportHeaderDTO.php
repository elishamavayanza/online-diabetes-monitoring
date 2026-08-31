<?php

namespace App\DTO\Response\Medical\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Informations d\'en-tête du rapport de suivi patient')]
class PatientFollowUpReportHeaderDTO
{
    public function __construct(
        #[OA\Property(example: '42')]
        public readonly string $patientId,
        #[OA\Property(example: 'Jean Dupont')]
        public readonly string $patientFullName,
        #[OA\Property(example: '1985-03-12', nullable: true)]
        public readonly ?string $dateOfBirth,
        #[OA\Property(example: 'Diabète de type 2', nullable: true)]
        public readonly ?string $diabetesType,
        #[OA\Property(example: 'Dr. Marie Kabila', nullable: true)]
        public readonly ?string $clinicianName,
        #[OA\Property(example: 'Centre Diabète Kinshasa', nullable: true)]
        public readonly ?string $organizationName,
        #[OA\Property(example: 'https://storage.diabcare.com/avatars/patient.jpg', nullable: true)]
        public readonly ?string $avatarUrl,
    ) {}
}
