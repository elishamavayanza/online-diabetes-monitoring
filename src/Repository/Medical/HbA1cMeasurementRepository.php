<?php

namespace App\Repository\Medical;

use App\Entity\Medical\HbA1cMeasurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HbA1cMeasurement>
 */
class HbA1cMeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HbA1cMeasurement::class);
    }

    /**
     * @return HbA1cMeasurement[]
     */
    public function findAboveThreshold(string $threshold): array
    {
        return $this->createQueryBuilder('hba')
            ->andWhere('hba.valuePercent >= :threshold')
            ->andWhere('hba.deletedAt IS NULL')
            ->setParameter('threshold', $threshold)
            ->orderBy('hba.measuredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
