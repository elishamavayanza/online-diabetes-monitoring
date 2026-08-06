<?php

namespace App\Repository\Healthcare;

use App\Entity\Healthcare\Department;
use App\Entity\Healthcare\HealthcareFacility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Department>
 */
class DepartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    /**
     * @return Department[]
     */
    public function findByFacility(HealthcareFacility $facility): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.facility = :facility')
            ->andWhere('d.deletedAt IS NULL')
            ->setParameter('facility', $facility)
            ->getQuery()
            ->getResult();
    }
}
