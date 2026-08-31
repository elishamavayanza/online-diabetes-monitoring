<?php

namespace App\Mapper\Medical;

use App\DTO\Response\Healthcare\Report\DistributionItemDTO;
use App\DTO\Response\Healthcare\Report\TrendPointDTO;
use App\DTO\Response\Healthcare\Report\TrendSeriesDTO;
use App\DTO\Response\Medical\Report\BloodPressureReportSectionDTO;
use App\DTO\Response\Medical\Report\GlucoseReportSectionDTO;
use App\DTO\Response\Medical\Report\HbA1cReportSectionDTO;
use App\DTO\Response\Medical\Report\LaboratoryReportItemDTO;
use App\DTO\Response\Medical\Report\LaboratoryReportSectionDTO;
use App\DTO\Response\Medical\Report\NutritionReportSectionDTO;
use App\DTO\Response\Medical\Report\PatientFollowUpReportDTO;
use App\DTO\Response\Medical\Report\PatientFollowUpReportHeaderDTO;
use App\DTO\Response\Medical\Report\PatientFollowUpReportPeriodDTO;
use App\DTO\Response\Medical\Report\PhysicalActivityReportSectionDTO;
use App\DTO\Response\Medical\Report\ReportMeasurementStatsDTO;
use App\DTO\Response\Medical\Report\TreatmentReportSectionDTO;
use App\DTO\Response\Medical\Report\WeightReportSectionDTO;
use App\Entity\Identity\Patient;
use App\Entity\Medical\LaboratoryResult;
use App\Enum\Medical\FollowUpReportElement;
use DateTimeImmutable;

class PatientFollowUpReportMapper
{
    /**
     * @param FollowUpReportElement[] $elements
     */
    public function mapToReport(
        Patient $patient,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        array $elements,
        array $sections,
        ?string $clinicianName,
        ?string $diabetesType,
    ): PatientFollowUpReportDTO {
        $selectedValues = array_map(static fn (FollowUpReportElement $element) => $element->value, $elements);
        $hasData = $this->detectHasData($sections);

        return new PatientFollowUpReportDTO(
            header: $this->mapHeader($patient, $clinicianName, $diabetesType),
            period: new PatientFollowUpReportPeriodDTO(
                from: $from->format('Y-m-d'),
                to: $to->format('Y-m-d'),
            ),
            selectedElements: $selectedValues,
            hasData: $hasData,
            glucose: $sections['glucose'] ?? null,
            hba1c: $sections['hba1c'] ?? null,
            bloodPressure: $sections['bloodPressure'] ?? null,
            weight: $sections['weight'] ?? null,
            treatment: $sections['treatment'] ?? null,
            physicalActivity: $sections['physicalActivity'] ?? null,
            nutrition: $sections['nutrition'] ?? null,
            laboratory: $sections['laboratory'] ?? null,
            generatedAt: (new DateTimeImmutable())->format(DATE_ATOM),
        );
    }

    public function mapGlucoseSection(array $data): GlucoseReportSectionDTO
    {
        return new GlucoseReportSectionDTO(
            stats: $this->mapStats($data, 'mg/dL'),
            ranges: $this->mapDistribution($data['ranges'] ?? []),
            trend: $this->mapTrend('Glycémie', 'mg/dL', $data['trend'] ?? []),
        );
    }

    public function mapHbA1cSection(array $data): HbA1cReportSectionDTO
    {
        return new HbA1cReportSectionDTO(
            stats: $this->mapStats($data, '%'),
            trend: $this->mapTrend('HbA1c', '%', $data['trend'] ?? []),
        );
    }

    public function mapBloodPressureSection(array $data): BloodPressureReportSectionDTO
    {
        $trends = [];
        if (!empty($data['trends']['systolic'])) {
            $trends[] = $this->mapTrend('Systolique', 'mmHg', $data['trends']['systolic']);
        }
        if (!empty($data['trends']['diastolic'])) {
            $trends[] = $this->mapTrend('Diastolique', 'mmHg', $data['trends']['diastolic']);
        }

        return new BloodPressureReportSectionDTO(
            systolic: $this->mapStats($data['systolic'] ?? [], 'mmHg'),
            diastolic: $this->mapStats($data['diastolic'] ?? [], 'mmHg'),
            trends: array_filter($trends),
        );
    }

    public function mapWeightSection(array $data): WeightReportSectionDTO
    {
        return new WeightReportSectionDTO(
            weight: $this->mapStats($data['weight'] ?? [], 'kg'),
            bmi: $this->mapStats($data['bmi'] ?? [], null),
            weightTrend: $this->mapTrend('Poids', 'kg', $data['trends']['weight'] ?? []),
            bmiTrend: $this->mapTrend('IMC', null, $data['trends']['bmi'] ?? []),
        );
    }

    public function mapTreatmentSection(array $data): TreatmentReportSectionDTO
    {
        return new TreatmentReportSectionDTO(
            activePrescriptions: (int) ($data['activePrescriptions'] ?? 0),
            adherenceRate: $data['adherenceRate'] ?? null,
            totalIntakes: (int) ($data['totalIntakes'] ?? 0),
            intakesByStatus: $this->mapDistribution($data['intakesByStatus'] ?? []),
        );
    }

    public function mapPhysicalActivitySection(array $data): PhysicalActivityReportSectionDTO
    {
        return new PhysicalActivityReportSectionDTO(
            sessions: (int) ($data['sessions'] ?? 0),
            totalMinutes: (int) ($data['totalMinutes'] ?? 0),
            averageMinutes: $data['averageMinutes'] ?? null,
            trend: $this->mapTrend('Activité physique', 'min', $data['trend'] ?? []),
        );
    }

    public function mapNutritionSection(array $data): NutritionReportSectionDTO
    {
        return new NutritionReportSectionDTO(
            totalMeals: (int) ($data['totalMeals'] ?? 0),
            mealsByType: $this->mapDistribution($data['mealsByType'] ?? []),
        );
    }

    /**
     * @param LaboratoryResult[] $results
     */
    public function mapLaboratorySection(array $results): LaboratoryReportSectionDTO
    {
        $items = array_map(
            static fn (LaboratoryResult $result) => new LaboratoryReportItemDTO(
                testName: $result->getTestName() ?? 'Examen',
                labName: $result->getLabName(),
                measuredAt: $result->getMeasuredAt()?->format('Y-m-d') ?? '',
                hasFile: $result->getFileUrl() !== null,
            ),
            $results
        );

        return new LaboratoryReportSectionDTO(
            count: count($items),
            results: $items,
        );
    }

    private function mapHeader(Patient $patient, ?string $clinicianName, ?string $diabetesType): PatientFollowUpReportHeaderDTO
    {
        $orgName = null;
        foreach ($patient->getOrganizationMemberships() as $membership) {
            if ($membership->getStatus()?->isActive() && $membership->getOrganization() !== null) {
                $orgName = $membership->getOrganization()->getName();
                break;
            }
        }

        return new PatientFollowUpReportHeaderDTO(
            patientId: (string) $patient->getId(),
            patientFullName: $patient->getFullName(),
            dateOfBirth: $patient->getDateOfBirth()?->format('Y-m-d'),
            diabetesType: $diabetesType,
            clinicianName: $clinicianName,
            organizationName: $orgName,
            avatarUrl: $patient->getAvatarUrl(),
        );
    }

    private function mapStats(array $data, ?string $unit): ReportMeasurementStatsDTO
    {
        return new ReportMeasurementStatsDTO(
            average: $data['average'] ?? null,
            minimum: $data['minimum'] ?? null,
            maximum: $data['maximum'] ?? null,
            count: (int) ($data['count'] ?? 0),
            unit: $unit,
        );
    }

    /**
     * @param array<int, array{label: string, count: int}> $items
     * @return DistributionItemDTO[]
     */
    private function mapDistribution(array $items): array
    {
        $total = array_sum(array_column($items, 'count'));

        return array_map(
            static fn (array $item) => new DistributionItemDTO(
                label: $item['label'],
                count: (int) $item['count'],
                percentage: $total > 0 ? round(((int) $item['count'] / $total) * 100, 1) : 0.0,
            ),
            $items
        );
    }

    /**
     * @param array<int, array{date: string, value: float}> $points
     */
    private function mapTrend(string $label, ?string $unit, array $points): ?TrendSeriesDTO
    {
        if ($points === []) {
            return null;
        }

        return new TrendSeriesDTO(
            label: $label,
            unit: $unit,
            points: array_map(
                static fn (array $point) => new TrendPointDTO($point['date'], $point['value']),
                $points
            ),
        );
    }

    private function detectHasData(array $sections): bool
    {
        foreach ($sections as $section) {
            if ($section === null) {
                continue;
            }

            if ($section instanceof GlucoseReportSectionDTO && $section->stats->count > 0) {
                return true;
            }
            if ($section instanceof HbA1cReportSectionDTO && $section->stats->count > 0) {
                return true;
            }
            if ($section instanceof BloodPressureReportSectionDTO && $section->systolic->count > 0) {
                return true;
            }
            if ($section instanceof WeightReportSectionDTO && $section->weight->count > 0) {
                return true;
            }
            if ($section instanceof TreatmentReportSectionDTO && ($section->totalIntakes > 0 || $section->activePrescriptions > 0)) {
                return true;
            }
            if ($section instanceof PhysicalActivityReportSectionDTO && $section->sessions > 0) {
                return true;
            }
            if ($section instanceof NutritionReportSectionDTO && $section->totalMeals > 0) {
                return true;
            }
            if ($section instanceof LaboratoryReportSectionDTO && $section->count > 0) {
                return true;
            }
        }

        return false;
    }
}
