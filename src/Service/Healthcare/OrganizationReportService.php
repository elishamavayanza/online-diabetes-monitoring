<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Medical\MedicalRecordStatus;
use App\Mapper\Healthcare\OrganizationReportMapper;
use App\Repository\Healthcare\OrganizationMembershipRepository;
use App\Repository\Healthcare\OrganizationReportRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use App\Service\Healthcare\Report\ReportPeriodResolver;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OrganizationReportService
{
  public function __construct(
    private readonly OrganizationReportRepository $reportRepository,
    private readonly OrganizationMembershipRepository $membershipRepository,
    private readonly OrganizationReportMapper $mapper,
    private readonly SecurityServiceInterface $securityService,
  ) {}

  public function generateReport(?string $from, ?string $to, ?string $period): Feedback
  {
    $feedback = new Feedback();

    try {
      $organization = $this->resolveOrganization();
      $this->securityService->checkOrganizationAccess($organization, SecurityAction::VIEW);

      $periodData = ReportPeriodResolver::resolve($from, $to, $period);
      $patientIds = $this->membershipRepository->findPatientIdsByOrganization($organization);

      $current = $this->collectStats($organization, $patientIds, $periodData['from'], $periodData['to']);
      $previous = $this->collectStats(
        $organization,
        $patientIds,
        $periodData['previousFrom'],
        $periodData['previousTo']
      );

      $report = $this->mapper->mapToReport($organization, $periodData, $current, $previous);

      return $feedback
        ->setData($report)
        ->setFlushDescription('Rapport organisation généré avec succès.')
        ->autoInitFlush();
    } catch (AccessDeniedException $e) {
      return $feedback
        ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
        ->setStatus(403)
        ->autoInitFlush();
    } catch (\Throwable $e) {
      return $feedback
        ->setErrorFlushDescription('Erreur lors de la génération du rapport : ' . $e->getMessage())
        ->setStatus(500)
        ->autoInitFlush();
    }
  }

  private function resolveOrganization(): HealthcareOrganization
  {
    if (!$this->securityService->isOrganizationAdmin() && !$this->securityService->isSuperAdmin()) {
      throw new AccessDeniedException('Cette opération est réservée aux administrateurs d\'organisation.');
    }

    $currentUser = $this->securityService->getCurrentUser();

    foreach ($currentUser->getOrganizationMemberships() as $membership) {
      if ($membership->getStatus()?->isActive() && $membership->getOrganization() !== null) {
        return $membership->getOrganization();
      }
    }

    throw new AccessDeniedException('Aucune organisation active trouvée pour cet utilisateur.');
  }

  private function collectStats(
    HealthcareOrganization $organization,
    array $patientIds,
    DateTimeImmutable $from,
    DateTimeImmutable $to
  ): array {
    $bloodPressure = $this->reportRepository->averageBloodPressure($patientIds, $from, $to);
    $weight = $this->reportRepository->averageWeightAndBmi($patientIds, $from, $to);
    $intakeStats = $this->reportRepository->medicationIntakeStats($patientIds, $from, $to);
    $activity = $this->reportRepository->physicalActivityStats($patientIds, $from, $to);
    $totalPatients = $this->membershipRepository->countPatientsByOrganization($organization);
    $patientsWithMeasurements = $this->reportRepository->countPatientsWithMeasurements($patientIds, $from, $to);

    $adherenceRate = $intakeStats['total'] > 0
      ? round(($intakeStats['taken'] / $intakeStats['total']) * 100, 1)
      : null;

    $complianceRate = $totalPatients > 0
      ? round(($patientsWithMeasurements / $totalPatients) * 100, 1)
      : null;

    return [
      'demographics' => [
        'totalPatients' => (float) $totalPatients,
        'activePatients' => (float) $totalPatients,
        'newPatients' => (float) $this->membershipRepository->countNewPatientsInPeriod($organization, $from, $to),
        'genderDistribution' => $this->membershipRepository->getGenderDistribution($organization),
        'ageGroups' => $this->membershipRepository->getAgeGroupDistribution($organization),
      ],
      'health' => [
        'averageGlucose' => $this->reportRepository->averageGlucose($patientIds, $from, $to),
        'glucoseMeasurements' => (float) $this->reportRepository->countGlucoseMeasurements($patientIds, $from, $to),
        'glucoseRanges' => $this->reportRepository->glucoseRangeDistribution($patientIds, $from, $to),
        'averageHbA1c' => $this->reportRepository->averageHbA1c($patientIds, $from, $to),
        'hba1cMeasurements' => (float) $this->reportRepository->countHbA1cMeasurements($patientIds, $from, $to),
        'averageSystolic' => $bloodPressure['systolic'],
        'averageDiastolic' => $bloodPressure['diastolic'],
        'averageBmi' => $weight['bmi'],
        'averageWeightKg' => $weight['weight'],
      ],
      'medical' => [
        'totalAppointments' => (float) $this->reportRepository->countAppointments($organization, $from, $to),
        'completedAppointments' => (float) $this->reportRepository->countAppointments($organization, $from, $to, 'COMPLETED'),
        'cancelledAppointments' => (float) $this->reportRepository->countAppointments($organization, $from, $to, 'CANCELLED'),
        'appointmentsByStatus' => $this->reportRepository->appointmentsByStatus($organization, $from, $to),
        'diagnosesCount' => (float) $this->reportRepository->countDiagnoses($patientIds, $from, $to),
        'openMedicalRecords' => (float) $this->reportRepository->countMedicalRecords($organization, MedicalRecordStatus::OPEN->value),
        'closedMedicalRecords' => (float) $this->reportRepository->countMedicalRecords($organization, MedicalRecordStatus::CLOSED->value),
      ],
      'treatments' => [
        'activePrescriptions' => (float) $this->reportRepository->countActivePrescriptions($organization, $to),
        'newPrescriptions' => (float) $this->reportRepository->countNewPrescriptions($organization, $from, $to),
        'adherenceRate' => $adherenceRate,
        'totalIntakes' => (float) $intakeStats['total'],
        'intakesByStatus' => [
          ['label' => 'TAKEN', 'count' => $intakeStats['taken']],
          ['label' => 'SKIPPED', 'count' => $intakeStats['skipped']],
          ['label' => 'DELAYED', 'count' => $intakeStats['delayed']],
        ],
      ],
      'lifestyle' => [
        'totalMeals' => (float) $this->reportRepository->countMeals($patientIds, $from, $to),
        'mealsByType' => $this->reportRepository->mealsByType($patientIds, $from, $to),
        'physicalActivitySessions' => (float) $activity['sessions'],
        'totalActivityMinutes' => (float) $activity['totalMinutes'],
        'averageActivityMinutes' => $activity['averageMinutes'],
      ],
      'trends' => [
        'patientsWithMeasurements' => (float) $patientsWithMeasurements,
        'measurementComplianceRate' => $complianceRate,
        'series' => [
          [
            'label' => 'Glycémie moyenne',
            'unit' => 'mg/dL',
            'points' => $this->reportRepository->glucoseTrend($patientIds, $from, $to),
          ],
          [
            'label' => 'Rendez-vous',
            'unit' => null,
            'points' => $this->reportRepository->appointmentTrend($organization, $from, $to),
          ],
        ],
      ],
    ];
  }
}
