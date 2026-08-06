<?php

namespace App\Repository\Medical;

use App\Entity\Medical\PhysicalActivityMeasurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PhysicalActivityMeasurement>
 */
class PhysicalActivityMeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhysicalActivityMeasurement::class);
    }

    /**
     * @return PhysicalActivityMeasurement[]
     */
    public function findByActivityType(string $activityType): array
    {
        return $this->createQueryBuilder('pam')
            ->andWhere('pam.activityType LIKE :activityType')
            ->andWhere('pam.deletedAt IS NULL')
            ->setParameter('activityType', '%' . $activityType . '%')
            ->orderBy('pam.measuredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
