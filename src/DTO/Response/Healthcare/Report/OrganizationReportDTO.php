<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Rapport analytique global de l\'organisation')]
class OrganizationReportDTO
{
    public function __construct(
        #[OA\Property(example: '12')]
        public readonly string $organizationId,
        #[OA\Property(example: 'Centre Diabète Kinshasa')]
        public readonly string $organizationName,
        public readonly ReportPeriodDTO $period,
        public readonly DemographicsReportDTO $demographics,
        public readonly HealthStatusReportDTO $healthStatus,
        public readonly MedicalActivityReportDTO $medicalActivity,
        public readonly TreatmentReportDTO $treatments,
        public readonly LifestyleReportDTO $lifestyle,
        public readonly TrendsReportDTO $trends,
        #[OA\Property(example: '2026-08-30T10:15:00+00:00')]
        public readonly string $generatedAt,
    ) {}
}
