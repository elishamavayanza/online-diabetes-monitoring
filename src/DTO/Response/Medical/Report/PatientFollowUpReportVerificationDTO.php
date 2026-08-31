<?php

namespace App\DTO\Response\Medical\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Résultat de vérification d\'un rapport de suivi patient')]
class PatientFollowUpReportVerificationDTO
{
    public function __construct(
        #[OA\Property(example: true)]
        public readonly bool $authentic,
        #[OA\Property(example: 'RPT-PAT-42-20260831')]
        public readonly string $reference,
        #[OA\Property(example: '42')]
        public readonly string $patientId,
        #[OA\Property(example: 'Jean Dupont')]
        public readonly string $patientFullName,
        #[OA\Property(example: 'Centre Diabète Kinshasa', nullable: true)]
        public readonly ?string $organizationName,
        #[OA\Property(example: '2026-08-01')]
        public readonly string $periodFrom,
        #[OA\Property(example: '2026-08-31')]
        public readonly string $periodTo,
        #[OA\Property(example: 'Rapport périodique d\'évolution patient DiabCare')]
        public readonly string $documentType,
        #[OA\Property(example: '2026-08-31T10:15:00+00:00')]
        public readonly string $verifiedAt,
        #[OA\Property(example: null, nullable: true)]
        public readonly ?string $message = null,
    ) {}
}
