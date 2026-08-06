<?php

namespace App\Repository\Medical;

use App\Entity\Medical\WeightMeasurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WeightMeasurement>
 */
class WeightMeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WeightMeasurement::class);
    }

    /**
     * @return WeightMeasurement[]
     */
    public function findByBmiRange(string $minBmi, string $maxBmi): array
    {
        return $this->createQueryBuilder('wm')
            ->andWhere('wm.bmi BETWEEN :minBmi AND :maxBmi')
            ->andWhere('wm.deletedAt IS NULL')
            ->setParameter('minBmi', $minBmi)
            ->setParameter('maxBmi', $maxBmi)
            ->orderBy('wm.measuredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
