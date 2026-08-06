<?php

namespace App\Repository\Medical;

use App\Entity\Medical\BloodPressureMeasurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BloodPressureMeasurement>
 */
class BloodPressureMeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BloodPressureMeasurement::class);
    }

    /**
     * @return BloodPressureMeasurement[]
     */
    public function findHypertensiveReadings(string $systolicThreshold, string $diastolicThreshold): array
    {
        return $this->createQueryBuilder('bpm')
            ->andWhere('bpm.systolic >= :systolicThreshold OR bpm.diastolic >= :diastolicThreshold')
            ->andWhere('bpm.deletedAt IS NULL')
            ->setParameter('systolicThreshold', $systolicThreshold)
            ->setParameter('diastolicThreshold', $diastolicThreshold)
            ->orderBy('bpm.measuredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
