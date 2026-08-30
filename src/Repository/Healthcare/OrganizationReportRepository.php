<?php

namespace App\Repository\Healthcare;

use App\Entity\Appointment\Appointment;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Medical\BloodGlucoseMeasurement;
use App\Entity\Medical\BloodPressureMeasurement;
use App\Entity\Medical\Diagnosis;
use App\Entity\Medical\HbA1cMeasurement;
use App\Entity\Medical\MedicalRecord;
use App\Entity\Medical\PhysicalActivityMeasurement;
use App\Entity\Medical\WeightMeasurement;
use App\Entity\Nutrition\Meal;
use App\Entity\Treatment\MedicationIntake;
use App\Entity\Treatment\Prescription;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrganizationReportRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, HealthcareOrganization::class);
  }

  public function averageGlucose(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): ?float
  {
    if ($patientIds === []) {
      return null;
    }

    $result = $this->getEntityManager()->createQueryBuilder()
      ->select('AVG(m.value)')
      ->from(BloodGlucoseMeasurement::class, 'm')
      ->andWhere('m.patient IN (:patients)')
      ->andWhere('m.measuredAt BETWEEN :from AND :to')
      ->andWhere('m.deletedAt IS NULL')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getSingleScalarResult();

    return $result !== null ? round((float) $result, 1) : null;
  }

  public function countGlucoseMeasurements(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): int
  {
    if ($patientIds === []) {
      return 0;
    }

    return (int) $this->getEntityManager()->createQueryBuilder()
      ->select('COUNT(m.id)')
      ->from(BloodGlucoseMeasurement::class, 'm')
      ->andWhere('m.patient IN (:patients)')
      ->andWhere('m.measuredAt BETWEEN :from AND :to')
      ->andWhere('m.deletedAt IS NULL')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getSingleScalarResult();
  }

  /**
   * @return array<int, array{label: string, count: int}>
   */
  public function glucoseRangeDistribution(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): array
  {
    if ($patientIds === []) {
      return [];
    }

    $conn = $this->getEntityManager()->getConnection();
    $placeholders = implode(',', array_fill(0, count($patientIds), '?'));
    $sql = <<<SQL
      SELECT
        CASE
          WHEN CAST(value AS DECIMAL(10,2)) < 70 THEN 'Hypoglycémie (<70)'
          WHEN CAST(value AS DECIMAL(10,2)) <= 180 THEN 'Cible (70-180)'
          ELSE 'Hyperglycémie (>180)'
        END AS label,
        COUNT(*) AS count
      FROM medical_blood_glucose_measurements
      WHERE patient_id IN ($placeholders)
        AND measured_at BETWEEN ? AND ?
        AND deleted_at IS NULL
      GROUP BY label
    SQL;

  $params = [...$patientIds, $from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s')];

    return $conn->executeQuery($sql, $params)->fetchAllAssociative();
  }

  public function averageHbA1c(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): ?float
  {
    if ($patientIds === []) {
      return null;
    }

    $result = $this->getEntityManager()->createQueryBuilder()
      ->select('AVG(m.valuePercent)')
      ->from(HbA1cMeasurement::class, 'm')
      ->andWhere('m.patient IN (:patients)')
      ->andWhere('m.measuredAt BETWEEN :from AND :to')
      ->andWhere('m.deletedAt IS NULL')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getSingleScalarResult();

    return $result !== null ? round((float) $result, 1) : null;
  }

  public function countHbA1cMeasurements(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): int
  {
    if ($patientIds === []) {
      return 0;
    }

    return (int) $this->getEntityManager()->createQueryBuilder()
      ->select('COUNT(m.id)')
      ->from(HbA1cMeasurement::class, 'm')
      ->andWhere('m.patient IN (:patients)')
      ->andWhere('m.measuredAt BETWEEN :from AND :to')
      ->andWhere('m.deletedAt IS NULL')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getSingleScalarResult();
  }

  public function averageBloodPressure(
    array $patientIds,
    DateTimeImmutable $from,
    DateTimeImmutable $to
  ): array {
    if ($patientIds === []) {
      return ['systolic' => null, 'diastolic' => null];
    }

    $row = $this->getEntityManager()->createQueryBuilder()
      ->select('AVG(m.systolic) AS systolic, AVG(m.diastolic) AS diastolic')
      ->from(BloodPressureMeasurement::class, 'm')
      ->andWhere('m.patient IN (:patients)')
      ->andWhere('m.measuredAt BETWEEN :from AND :to')
      ->andWhere('m.deletedAt IS NULL')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getOneOrNullResult();

    return [
      'systolic' => isset($row['systolic']) ? round((float) $row['systolic'], 1) : null,
      'diastolic' => isset($row['diastolic']) ? round((float) $row['diastolic'], 1) : null,
    ];
  }

  public function averageWeightAndBmi(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): array
  {
    if ($patientIds === []) {
      return ['weight' => null, 'bmi' => null];
    }

    $row = $this->getEntityManager()->createQueryBuilder()
      ->select('AVG(m.valueKg) AS weight, AVG(m.bmi) AS bmi')
      ->from(WeightMeasurement::class, 'm')
      ->andWhere('m.patient IN (:patients)')
      ->andWhere('m.measuredAt BETWEEN :from AND :to')
      ->andWhere('m.deletedAt IS NULL')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getOneOrNullResult();

    return [
      'weight' => isset($row['weight']) ? round((float) $row['weight'], 1) : null,
      'bmi' => isset($row['bmi']) ? round((float) $row['bmi'], 1) : null,
    ];
  }

  public function countAppointments(
    HealthcareOrganization $organization,
    DateTimeImmutable $from,
    DateTimeImmutable $to,
    ?string $status = null
  ): int {
    $qb = $this->getEntityManager()->createQueryBuilder()
      ->select('COUNT(a.id)')
      ->from(Appointment::class, 'a')
      ->andWhere('a.organization = :organization')
      ->andWhere('a.scheduledAt BETWEEN :from AND :to')
      ->andWhere('a.deletedAt IS NULL')
      ->setParameter('organization', $organization)
      ->setParameter('from', $from)
      ->setParameter('to', $to);

    if ($status !== null) {
      $qb->andWhere('a.status = :status')->setParameter('status', $status);
    }

    return (int) $qb->getQuery()->getSingleScalarResult();
  }

  /**
   * @return array<int, array{label: string, count: int}>
   */
  public function appointmentsByStatus(
    HealthcareOrganization $organization,
    DateTimeImmutable $from,
    DateTimeImmutable $to
  ): array {
    $rows = $this->getEntityManager()->createQueryBuilder()
      ->select('a.status AS label, COUNT(a.id) AS count')
      ->from(Appointment::class, 'a')
      ->andWhere('a.organization = :organization')
      ->andWhere('a.scheduledAt BETWEEN :from AND :to')
      ->andWhere('a.deletedAt IS NULL')
      ->groupBy('a.status')
      ->setParameter('organization', $organization)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getArrayResult();

    return array_map(
      static fn (array $row) => [
        'label' => $row['label']?->value ?? 'UNKNOWN',
        'count' => (int) $row['count'],
      ],
      $rows
    );
  }

  public function countDiagnoses(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): int
  {
    if ($patientIds === []) {
      return 0;
    }

    return (int) $this->getEntityManager()->createQueryBuilder()
      ->select('COUNT(d.id)')
      ->from(Diagnosis::class, 'd')
      ->andWhere('d.patient IN (:patients)')
      ->andWhere('d.diagnosedAt BETWEEN :from AND :to')
      ->andWhere('d.deletedAt IS NULL')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getSingleScalarResult();
  }

  public function countMedicalRecords(
    HealthcareOrganization $organization,
    ?string $status = null
  ): int {
    $qb = $this->getEntityManager()->createQueryBuilder()
      ->select('COUNT(r.id)')
      ->from(MedicalRecord::class, 'r')
      ->andWhere('r.organization = :organization')
      ->andWhere('r.deletedAt IS NULL')
      ->setParameter('organization', $organization);

    if ($status !== null) {
      $qb->andWhere('r.status = :status')->setParameter('status', $status);
    }

    return (int) $qb->getQuery()->getSingleScalarResult();
  }

  public function countActivePrescriptions(
    HealthcareOrganization $organization,
    DateTimeImmutable $at
  ): int {
    return (int) $this->getEntityManager()->createQueryBuilder()
      ->select('COUNT(p.id)')
      ->from(Prescription::class, 'p')
      ->andWhere('p.organization = :organization')
      ->andWhere('p.startDate <= :at')
      ->andWhere('p.endDate IS NULL OR p.endDate >= :at')
      ->andWhere('p.deletedAt IS NULL')
      ->setParameter('organization', $organization)
      ->setParameter('at', $at)
      ->getQuery()
      ->getSingleScalarResult();
  }

  public function countNewPrescriptions(
    HealthcareOrganization $organization,
    DateTimeImmutable $from,
    DateTimeImmutable $to
  ): int {
    return (int) $this->getEntityManager()->createQueryBuilder()
      ->select('COUNT(p.id)')
      ->from(Prescription::class, 'p')
      ->andWhere('p.organization = :organization')
      ->andWhere('p.startDate BETWEEN :from AND :to')
      ->andWhere('p.deletedAt IS NULL')
      ->setParameter('organization', $organization)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getSingleScalarResult();
  }

  /**
   * @return array{total: int, taken: int, skipped: int, delayed: int}
   */
  public function medicationIntakeStats(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): array
  {
    if ($patientIds === []) {
      return ['total' => 0, 'taken' => 0, 'skipped' => 0, 'delayed' => 0];
    }

    $rows = $this->getEntityManager()->createQueryBuilder()
      ->select('i.status AS status, COUNT(i.id) AS count')
      ->from(MedicationIntake::class, 'i')
      ->andWhere('i.patient IN (:patients)')
      ->andWhere('i.takenAt BETWEEN :from AND :to')
      ->andWhere('i.deletedAt IS NULL')
      ->groupBy('i.status')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getArrayResult();

    $stats = ['total' => 0, 'taken' => 0, 'skipped' => 0, 'delayed' => 0];
    foreach ($rows as $row) {
      $count = (int) $row['count'];
      $stats['total'] += $count;
      $status = $row['status']?->value ?? '';
      match ($status) {
        'TAKEN' => $stats['taken'] = $count,
        'SKIPPED' => $stats['skipped'] = $count,
        'DELAYED' => $stats['delayed'] = $count,
        default => null,
      };
    }

    return $stats;
  }

  public function countMeals(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): int
  {
    if ($patientIds === []) {
      return 0;
    }

    return (int) $this->getEntityManager()->createQueryBuilder()
      ->select('COUNT(m.id)')
      ->from(Meal::class, 'm')
      ->andWhere('m.patient IN (:patients)')
      ->andWhere('m.measuredAt BETWEEN :from AND :to')
      ->andWhere('m.deletedAt IS NULL')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getSingleScalarResult();
  }

  /**
   * @return array<int, array{label: string, count: int}>
   */
  public function mealsByType(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): array
  {
    if ($patientIds === []) {
      return [];
    }

    $rows = $this->getEntityManager()->createQueryBuilder()
      ->select('m.mealType AS label, COUNT(m.id) AS count')
      ->from(Meal::class, 'm')
      ->andWhere('m.patient IN (:patients)')
      ->andWhere('m.measuredAt BETWEEN :from AND :to')
      ->andWhere('m.deletedAt IS NULL')
      ->groupBy('m.mealType')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getArrayResult();

    return array_map(
      static fn (array $row) => [
        'label' => $row['label']?->value ?? 'UNKNOWN',
        'count' => (int) $row['count'],
      ],
      $rows
    );
  }

  /**
   * @return array{sessions: int, totalMinutes: int, averageMinutes: ?float}
   */
  public function physicalActivityStats(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): array
  {
    if ($patientIds === []) {
      return ['sessions' => 0, 'totalMinutes' => 0, 'averageMinutes' => null];
    }

    $row = $this->getEntityManager()->createQueryBuilder()
      ->select('COUNT(m.id) AS sessions, SUM(m.durationMinutes) AS totalMinutes, AVG(m.durationMinutes) AS averageMinutes')
      ->from(PhysicalActivityMeasurement::class, 'm')
      ->andWhere('m.patient IN (:patients)')
      ->andWhere('m.measuredAt BETWEEN :from AND :to')
      ->andWhere('m.deletedAt IS NULL')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getOneOrNullResult();

    return [
      'sessions' => (int) ($row['sessions'] ?? 0),
      'totalMinutes' => (int) ($row['totalMinutes'] ?? 0),
      'averageMinutes' => isset($row['averageMinutes']) ? round((float) $row['averageMinutes'], 1) : null,
    ];
  }

  public function countPatientsWithMeasurements(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): int
  {
    if ($patientIds === []) {
      return 0;
    }

    return (int) $this->getEntityManager()->createQueryBuilder()
      ->select('COUNT(DISTINCT m.patient)')
      ->from(BloodGlucoseMeasurement::class, 'm')
      ->andWhere('m.patient IN (:patients)')
      ->andWhere('m.measuredAt BETWEEN :from AND :to')
      ->andWhere('m.deletedAt IS NULL')
      ->setParameter('patients', $patientIds)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getSingleScalarResult();
  }

  /**
   * @return array<int, array{date: string, value: float}>
   */
  public function glucoseTrend(array $patientIds, DateTimeImmutable $from, DateTimeImmutable $to): array
  {
    if ($patientIds === []) {
      return [];
    }

    $conn = $this->getEntityManager()->getConnection();
    $placeholders = implode(',', array_fill(0, count($patientIds), '?'));
    $sql = <<<SQL
      SELECT DATE(measured_at) AS date, AVG(CAST(value AS DECIMAL(10,2))) AS value
      FROM medical_blood_glucose_measurements
      WHERE patient_id IN ($placeholders)
        AND measured_at BETWEEN ? AND ?
        AND deleted_at IS NULL
      GROUP BY DATE(measured_at)
      ORDER BY date ASC
    SQL;

    $params = [...$patientIds, $from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s')];
    $rows = $conn->executeQuery($sql, $params)->fetchAllAssociative();

    return array_map(
      static fn (array $row) => [
        'date' => $row['date'],
        'value' => round((float) $row['value'], 1),
      ],
      $rows
    );
  }

  /**
   * @return array<int, array{date: string, value: float}>
   */
  public function appointmentTrend(
    HealthcareOrganization $organization,
    DateTimeImmutable $from,
    DateTimeImmutable $to
  ): array {
    $conn = $this->getEntityManager()->getConnection();
    $sql = <<<SQL
      SELECT DATE(scheduled_at) AS date, COUNT(*) AS value
      FROM appointment_appointments
      WHERE organization_id = :organizationId
        AND scheduled_at BETWEEN :from AND :to
        AND deleted_at IS NULL
      GROUP BY DATE(scheduled_at)
      ORDER BY date ASC
    SQL;

    $rows = $conn->executeQuery($sql, [
      'organizationId' => $organization->getId(),
      'from' => $from->format('Y-m-d H:i:s'),
      'to' => $to->format('Y-m-d H:i:s'),
    ])->fetchAllAssociative();

    return array_map(
      static fn (array $row) => [
        'date' => $row['date'],
        'value' => (float) $row['value'],
      ],
      $rows
    );
  }
}
