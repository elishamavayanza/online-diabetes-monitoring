<?php

namespace App\DTO\Response\Medical\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Rapport périodique d\'évolution d\'un patient')]
class PatientFollowUpReportDTO
{
    /**
     * @param string[] $selectedElements
     */
    public function __construct(
        public readonly PatientFollowUpReportHeaderDTO $header,
        public readonly PatientFollowUpReportPeriodDTO $period,
        public readonly array $selectedElements,
        #[OA\Property(description: 'Indique si au moins une section contient des données')]
        public readonly bool $hasData,
        public readonly ?GlucoseReportSectionDTO $glucose = null,
        public readonly ?HbA1cReportSectionDTO $hba1c = null,
        public readonly ?BloodPressureReportSectionDTO $bloodPressure = null,
        public readonly ?WeightReportSectionDTO $weight = null,
        public readonly ?TreatmentReportSectionDTO $treatment = null,
        public readonly ?PhysicalActivityReportSectionDTO $physicalActivity = null,
        public readonly ?NutritionReportSectionDTO $nutrition = null,
        public readonly ?LaboratoryReportSectionDTO $laboratory = null,
        #[OA\Property(example: '2026-08-30T10:15:00+00:00')]
        public readonly string $generatedAt = '',
    ) {}
}
