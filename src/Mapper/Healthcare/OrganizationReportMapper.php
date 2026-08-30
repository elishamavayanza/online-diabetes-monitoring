<?php

namespace App\Mapper\Healthcare;

use App\DTO\Response\Healthcare\Report\DemographicsReportDTO;
use App\DTO\Response\Healthcare\Report\DistributionItemDTO;
use App\DTO\Response\Healthcare\Report\HealthStatusReportDTO;
use App\DTO\Response\Healthcare\Report\LifestyleReportDTO;
use App\DTO\Response\Healthcare\Report\MedicalActivityReportDTO;
use App\DTO\Response\Healthcare\Report\OrganizationReportDTO;
use App\DTO\Response\Healthcare\Report\ReportPeriodDTO;
use App\DTO\Response\Healthcare\Report\StatisticValueDTO;
use App\DTO\Response\Healthcare\Report\TrendPointDTO;
use App\DTO\Response\Healthcare\Report\TrendSeriesDTO;
use App\DTO\Response\Healthcare\Report\TrendsReportDTO;
use App\DTO\Response\Healthcare\Report\TreatmentReportDTO;
use App\Entity\Healthcare\HealthcareOrganization;
use DateTimeImmutable;

class OrganizationReportMapper
{
  public function mapToReport(
    HealthcareOrganization $organization,
    array $period,
    array $current,
    array $previous
  ): OrganizationReportDTO {
    return new OrganizationReportDTO(
      organizationId: (string) $organization->getId(),
      organizationName: $organization->getName() ?? '',
      period: new ReportPeriodDTO(
        from: $period['from']->format('Y-m-d'),
        to: $period['to']->format('Y-m-d'),
        previousFrom: $period['previousFrom']->format('Y-m-d'),
        previousTo: $period['previousTo']->format('Y-m-d'),
        preset: $period['preset'],
      ),
      demographics: $this->mapDemographics($current['demographics'], $previous['demographics']),
      healthStatus: $this->mapHealthStatus($current['health'], $previous['health']),
      medicalActivity: $this->mapMedicalActivity($current['medical'], $previous['medical']),
      treatments: $this->mapTreatments($current['treatments'], $previous['treatments']),
      lifestyle: $this->mapLifestyle($current['lifestyle'], $previous['lifestyle']),
      trends: $this->mapTrends($current['trends'], $previous['trends']),
      generatedAt: (new DateTimeImmutable())->format(DATE_ATOM),
    );
  }

  private function mapDemographics(array $current, array $previous): DemographicsReportDTO
  {
    return new DemographicsReportDTO(
      totalPatients: $this->stat($current['totalPatients'], $previous['totalPatients']),
      activePatients: $this->stat($current['activePatients'], $previous['activePatients']),
      newPatients: $this->stat($current['newPatients'], $previous['newPatients']),
      genderDistribution: $this->distribution($current['genderDistribution']),
      ageGroups: $this->distribution($current['ageGroups']),
    );
  }

  private function mapHealthStatus(array $current, array $previous): HealthStatusReportDTO
  {
    return new HealthStatusReportDTO(
      averageGlucose: $this->stat($current['averageGlucose'], $previous['averageGlucose'], 'mg/dL'),
      glucoseMeasurements: $this->stat($current['glucoseMeasurements'], $previous['glucoseMeasurements']),
      glucoseRanges: $this->distribution($current['glucoseRanges']),
      averageHbA1c: $this->stat($current['averageHbA1c'], $previous['averageHbA1c'], '%'),
      hba1cMeasurements: $this->stat($current['hba1cMeasurements'], $previous['hba1cMeasurements']),
      averageSystolic: $this->stat($current['averageSystolic'], $previous['averageSystolic'], 'mmHg'),
      averageDiastolic: $this->stat($current['averageDiastolic'], $previous['averageDiastolic'], 'mmHg'),
      averageBmi: $this->stat($current['averageBmi'], $previous['averageBmi']),
      averageWeightKg: $this->stat($current['averageWeightKg'], $previous['averageWeightKg'], 'kg'),
    );
  }

  private function mapMedicalActivity(array $current, array $previous): MedicalActivityReportDTO
  {
    return new MedicalActivityReportDTO(
      totalAppointments: $this->stat($current['totalAppointments'], $previous['totalAppointments']),
      completedAppointments: $this->stat($current['completedAppointments'], $previous['completedAppointments']),
      cancelledAppointments: $this->stat($current['cancelledAppointments'], $previous['cancelledAppointments']),
      appointmentsByStatus: $this->distribution($current['appointmentsByStatus']),
      diagnosesCount: $this->stat($current['diagnosesCount'], $previous['diagnosesCount']),
      openMedicalRecords: $this->stat($current['openMedicalRecords'], $previous['openMedicalRecords']),
      closedMedicalRecords: $this->stat($current['closedMedicalRecords'], $previous['closedMedicalRecords']),
    );
  }

  private function mapTreatments(array $current, array $previous): TreatmentReportDTO
  {
    return new TreatmentReportDTO(
      activePrescriptions: $this->stat($current['activePrescriptions'], $previous['activePrescriptions']),
      newPrescriptions: $this->stat($current['newPrescriptions'], $previous['newPrescriptions']),
      adherenceRate: $this->stat($current['adherenceRate'], $previous['adherenceRate'], '%'),
      totalIntakes: $this->stat($current['totalIntakes'], $previous['totalIntakes']),
      intakesByStatus: $this->distribution($current['intakesByStatus']),
    );
  }

  private function mapLifestyle(array $current, array $previous): LifestyleReportDTO
  {
    return new LifestyleReportDTO(
      totalMeals: $this->stat($current['totalMeals'], $previous['totalMeals']),
      mealsByType: $this->distribution($current['mealsByType']),
      physicalActivitySessions: $this->stat($current['physicalActivitySessions'], $previous['physicalActivitySessions']),
      totalActivityMinutes: $this->stat($current['totalActivityMinutes'], $previous['totalActivityMinutes'], 'min'),
      averageActivityMinutes: $this->stat($current['averageActivityMinutes'], $previous['averageActivityMinutes'], 'min'),
    );
  }

  private function mapTrends(array $current, array $previous): TrendsReportDTO
  {
    $series = [];
    foreach ($current['series'] as $item) {
      $series[] = new TrendSeriesDTO(
        label: $item['label'],
        unit: $item['unit'] ?? null,
        points: array_map(
          static fn (array $point) => new TrendPointDTO($point['date'], $point['value']),
          $item['points']
        ),
      );
    }

    return new TrendsReportDTO(
      patientsWithMeasurements: $this->stat($current['patientsWithMeasurements'], $previous['patientsWithMeasurements']),
      measurementComplianceRate: $this->stat($current['measurementComplianceRate'], $previous['measurementComplianceRate'], '%'),
      series: $series,
    );
  }

  private function stat(?float $current, ?float $previous, ?string $unit = null): StatisticValueDTO
  {
    return new StatisticValueDTO(
      value: $current,
      previousValue: $previous,
      changePercent: self::changePercent($current, $previous),
      unit: $unit,
    );
  }

  /**
   * @param array<int, array{label: string, count: int}> $items
   * @return DistributionItemDTO[]
   */
  private function distribution(array $items): array
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

  public static function changePercent(?float $current, ?float $previous): ?float
  {
    if ($current === null || $previous === null || $previous == 0.0) {
      return null;
    }

    return round((($current - $previous) / $previous) * 100, 1);
  }
}
