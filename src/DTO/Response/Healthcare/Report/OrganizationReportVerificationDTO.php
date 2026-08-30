<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Résultat de vérification d\'un rapport organisation')]
class OrganizationReportVerificationDTO
{
    public function __construct(
        #[OA\Property(example: true)]
        public readonly bool $authentic,
        #[OA\Property(example: 'RPT-ORG-12-20260830')]
        public readonly string $reference,
        #[OA\Property(example: '12')]
        public readonly string $organizationId,
        #[OA\Property(example: 'Centre Diabète Kinshasa')]
        public readonly string $organizationName,
        #[OA\Property(example: '2026-08-01')]
        public readonly string $periodFrom,
        #[OA\Property(example: '2026-08-30')]
        public readonly string $periodTo,
        #[OA\Property(example: 'Rapport analytique organisationnel DiabCare')]
        public readonly string $documentType,
        #[OA\Property(example: '2026-08-30T10:15:00+00:00')]
        public readonly string $verifiedAt,
        #[OA\Property(example: null, nullable: true)]
        public readonly ?string $message = null,
    ) {}
}
