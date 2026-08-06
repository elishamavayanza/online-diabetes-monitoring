<?php

namespace App\Repository\Medical;

use App\Entity\Medical\BloodGlucoseMeasurement;
use App\Entity\Medical\GlucoseContext;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BloodGlucoseMeasurement>
 */
class BloodGlucoseMeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BloodGlucoseMeasurement::class);
    }

    /**
     * @return BloodGlucoseMeasurement[]
     */
    public function findByContext(GlucoseContext $context): array
    {
        return $this->createQueryBuilder('bgm')
            ->andWhere('bgm.context = :context')
            ->andWhere('bgm.deletedAt IS NULL')
            ->setParameter('context', $context)
            ->orderBy('bgm.measuredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
