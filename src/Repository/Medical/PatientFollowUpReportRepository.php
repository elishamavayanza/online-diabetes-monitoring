<?php

namespace App\Repository\Medical;

use App\Entity\Identity\Patient;
use App\Entity\Medical\BloodGlucoseMeasurement;
use App\Entity\Medical\BloodPressureMeasurement;
use App\Entity\Medical\HbA1cMeasurement;
use App\Entity\Medical\LaboratoryResult;
use App\Entity\Medical\PhysicalActivityMeasurement;
use App\Entity\Medical\WeightMeasurement;
use App\Entity\Treatment\Prescription;
use App\Repository\Healthcare\OrganizationReportRepository;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PatientFollowUpReportRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly OrganizationReportRepository $organizationReportRepository,
    ) {
        parent::__construct($registry, Patient::class);
    }

    /**
     * @return int[]
     */
    private function patientIds(Patient $patient): array
    {
        return [$patient->getId()];
    }

    public function glucoseStats(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $ids = $this->patientIds($patient);

        $row = $this->getEntityManager()->createQueryBuilder()
            ->select('AVG(m.value) AS average, MIN(m.value) AS minimum, MAX(m.value) AS maximum, COUNT(m.id) AS count')
            ->from(BloodGlucoseMeasurement::class, 'm')
            ->andWhere('m.patient = :patient')
            ->andWhere('m.measuredAt BETWEEN :from AND :to')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getOneOrNullResult();

        return [
            'average' => isset($row['average']) ? round((float) $row['average'], 1) : null,
            'minimum' => isset($row['minimum']) ? round((float) $row['minimum'], 1) : null,
            'maximum' => isset($row['maximum']) ? round((float) $row['maximum'], 1) : null,
            'count' => (int) ($row['count'] ?? 0),
            'ranges' => $this->organizationReportRepository->glucoseRangeDistribution($ids, $from, $to),
            'trend' => $this->organizationReportRepository->glucoseTrend($ids, $from, $to),
        ];
    }

    public function hba1cStats(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $ids = $this->patientIds($patient);

        $row = $this->getEntityManager()->createQueryBuilder()
            ->select('AVG(m.valuePercent) AS average, MIN(m.valuePercent) AS minimum, MAX(m.valuePercent) AS maximum, COUNT(m.id) AS count')
            ->from(HbA1cMeasurement::class, 'm')
            ->andWhere('m.patient = :patient')
            ->andWhere('m.measuredAt BETWEEN :from AND :to')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getOneOrNullResult();

        return [
            'average' => isset($row['average']) ? round((float) $row['average'], 1) : null,
            'minimum' => isset($row['minimum']) ? round((float) $row['minimum'], 1) : null,
            'maximum' => isset($row['maximum']) ? round((float) $row['maximum'], 1) : null,
            'count' => (int) ($row['count'] ?? 0),
            'trend' => $this->hba1cTrend($patient, $from, $to),
        ];
    }

    /**
     * @return array<int, array{date: string, value: float}>
     */
    public function hba1cTrend(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<SQL
            SELECT DATE(measured_at) AS date, AVG(value_percent) AS value
            FROM medical_hba1c_measurements
            WHERE patient_id = :patientId
              AND measured_at BETWEEN :from AND :to
              AND deleted_at IS NULL
            GROUP BY DATE(measured_at)
            ORDER BY date ASC
        SQL;

        $rows = $conn->executeQuery($sql, [
            'patientId' => $patient->getId(),
            'from' => $from->format('Y-m-d H:i:s'),
            'to' => $to->format('Y-m-d H:i:s'),
        ])->fetchAllAssociative();

        return array_map(
            static fn (array $row) => [
                'date' => $row['date'],
                'value' => round((float) $row['value'], 1),
            ],
            $rows
        );
    }

    public function bloodPressureStats(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $row = $this->getEntityManager()->createQueryBuilder()
            ->select(
                'AVG(m.systolic) AS avgSystolic, MIN(m.systolic) AS minSystolic, MAX(m.systolic) AS maxSystolic, COUNT(m.id) AS countSystolic',
                'AVG(m.diastolic) AS avgDiastolic, MIN(m.diastolic) AS minDiastolic, MAX(m.diastolic) AS maxDiastolic'
            )
            ->from(BloodPressureMeasurement::class, 'm')
            ->andWhere('m.patient = :patient')
            ->andWhere('m.measuredAt BETWEEN :from AND :to')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getOneOrNullResult();

        return [
            'systolic' => [
                'average' => isset($row['avgSystolic']) ? round((float) $row['avgSystolic'], 1) : null,
                'minimum' => isset($row['minSystolic']) ? round((float) $row['minSystolic'], 1) : null,
                'maximum' => isset($row['maxSystolic']) ? round((float) $row['maxSystolic'], 1) : null,
                'count' => (int) ($row['countSystolic'] ?? 0),
            ],
            'diastolic' => [
                'average' => isset($row['avgDiastolic']) ? round((float) $row['avgDiastolic'], 1) : null,
                'minimum' => isset($row['minDiastolic']) ? round((float) $row['minDiastolic'], 1) : null,
                'maximum' => isset($row['maxDiastolic']) ? round((float) $row['maxDiastolic'], 1) : null,
                'count' => (int) ($row['countSystolic'] ?? 0),
            ],
            'trends' => $this->bloodPressureTrends($patient, $from, $to),
        ];
    }

    /**
     * @return array{systolic: array<int, array{date: string, value: float}>, diastolic: array<int, array{date: string, value: float}>}
     */
    public function bloodPressureTrends(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<SQL
            SELECT DATE(measured_at) AS date,
                   AVG(systolic) AS systolic,
                   AVG(diastolic) AS diastolic
            FROM medical_blood_pressure_measurements
            WHERE patient_id = :patientId
              AND measured_at BETWEEN :from AND :to
              AND deleted_at IS NULL
            GROUP BY DATE(measured_at)
            ORDER BY date ASC
        SQL;

        $rows = $conn->executeQuery($sql, [
            'patientId' => $patient->getId(),
            'from' => $from->format('Y-m-d H:i:s'),
            'to' => $to->format('Y-m-d H:i:s'),
        ])->fetchAllAssociative();

        $systolic = [];
        $diastolic = [];

        foreach ($rows as $row) {
            $systolic[] = ['date' => $row['date'], 'value' => round((float) $row['systolic'], 1)];
            $diastolic[] = ['date' => $row['date'], 'value' => round((float) $row['diastolic'], 1)];
        }

        return compact('systolic', 'diastolic');
    }

    public function weightStats(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $row = $this->getEntityManager()->createQueryBuilder()
            ->select(
                'AVG(m.valueKg) AS avgWeight, MIN(m.valueKg) AS minWeight, MAX(m.valueKg) AS maxWeight',
                'AVG(m.bmi) AS avgBmi, MIN(m.bmi) AS minBmi, MAX(m.bmi) AS maxBmi, COUNT(m.id) AS count'
            )
            ->from(WeightMeasurement::class, 'm')
            ->andWhere('m.patient = :patient')
            ->andWhere('m.measuredAt BETWEEN :from AND :to')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getOneOrNullResult();

        return [
            'weight' => [
                'average' => isset($row['avgWeight']) ? round((float) $row['avgWeight'], 1) : null,
                'minimum' => isset($row['minWeight']) ? round((float) $row['minWeight'], 1) : null,
                'maximum' => isset($row['maxWeight']) ? round((float) $row['maxWeight'], 1) : null,
                'count' => (int) ($row['count'] ?? 0),
            ],
            'bmi' => [
                'average' => isset($row['avgBmi']) ? round((float) $row['avgBmi'], 1) : null,
                'minimum' => isset($row['minBmi']) ? round((float) $row['minBmi'], 1) : null,
                'maximum' => isset($row['maxBmi']) ? round((float) $row['maxBmi'], 1) : null,
                'count' => (int) ($row['count'] ?? 0),
            ],
            'trends' => $this->weightTrends($patient, $from, $to),
        ];
    }

    /**
     * @return array{weight: array<int, array{date: string, value: float}>, bmi: array<int, array{date: string, value: float}>}
     */
    public function weightTrends(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<SQL
            SELECT DATE(measured_at) AS date,
                   AVG(value_kg) AS weight,
                   AVG(bmi) AS bmi
            FROM medical_weight_measurements
            WHERE patient_id = :patientId
              AND measured_at BETWEEN :from AND :to
              AND deleted_at IS NULL
            GROUP BY DATE(measured_at)
            ORDER BY date ASC
        SQL;

        $rows = $conn->executeQuery($sql, [
            'patientId' => $patient->getId(),
            'from' => $from->format('Y-m-d H:i:s'),
            'to' => $to->format('Y-m-d H:i:s'),
        ])->fetchAllAssociative();

        $weight = [];
        $bmi = [];

        foreach ($rows as $row) {
            $weight[] = ['date' => $row['date'], 'value' => round((float) $row['weight'], 1)];
            if ($row['bmi'] !== null) {
                $bmi[] = ['date' => $row['date'], 'value' => round((float) $row['bmi'], 1)];
            }
        }

        return compact('weight', 'bmi');
    }

    public function treatmentStats(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $ids = $this->patientIds($patient);
        $intakeStats = $this->organizationReportRepository->medicationIntakeStats($ids, $from, $to);

        $activePrescriptions = (int) $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Prescription::class, 'p')
            ->andWhere('p.patient = :patient')
            ->andWhere('p.startDate <= :to')
            ->andWhere('p.endDate IS NULL OR p.endDate >= :from')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getSingleScalarResult();

        $adherenceRate = $intakeStats['total'] > 0
            ? round(($intakeStats['taken'] / $intakeStats['total']) * 100, 1)
            : null;

        return [
            'activePrescriptions' => $activePrescriptions,
            'adherenceRate' => $adherenceRate,
            'totalIntakes' => $intakeStats['total'],
            'intakesByStatus' => [
                ['label' => 'TAKEN', 'count' => $intakeStats['taken']],
                ['label' => 'SKIPPED', 'count' => $intakeStats['skipped']],
                ['label' => 'DELAYED', 'count' => $intakeStats['delayed']],
            ],
        ];
    }

    public function physicalActivityStats(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $ids = $this->patientIds($patient);
        $stats = $this->organizationReportRepository->physicalActivityStats($ids, $from, $to);

        return $stats + [
            'trend' => $this->activityTrend($patient, $from, $to),
        ];
    }

    /**
     * @return array<int, array{date: string, value: float}>
     */
    public function activityTrend(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<SQL
            SELECT DATE(measured_at) AS date, SUM(duration_minutes) AS value
            FROM medical_physical_activity_measurements
            WHERE patient_id = :patientId
              AND measured_at BETWEEN :from AND :to
              AND deleted_at IS NULL
            GROUP BY DATE(measured_at)
            ORDER BY date ASC
        SQL;

        $rows = $conn->executeQuery($sql, [
            'patientId' => $patient->getId(),
            'from' => $from->format('Y-m-d H:i:s'),
            'to' => $to->format('Y-m-d H:i:s'),
        ])->fetchAllAssociative();

        return array_map(
            static fn (array $row) => [
                'date' => $row['date'],
                'value' => round((float) $row['value'], 1),
            ],
            $rows
        );
    }

    public function nutritionStats(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $ids = $this->patientIds($patient);

        return [
            'totalMeals' => $this->organizationReportRepository->countMeals($ids, $from, $to),
            'mealsByType' => $this->organizationReportRepository->mealsByType($ids, $from, $to),
        ];
    }

    /**
     * @return LaboratoryResult[]
     */
    public function laboratoryResults(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('l')
            ->from(LaboratoryResult::class, 'l')
            ->andWhere('l.patient = :patient')
            ->andWhere('l.measuredAt BETWEEN :from AND :to')
            ->andWhere('l.deletedAt IS NULL')
            ->orderBy('l.measuredAt', 'DESC')
            ->setParameter('patient', $patient)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult();
    }
}
