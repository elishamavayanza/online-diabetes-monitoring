<?php

namespace App\Repository\Medical;

use App\Entity\Medical\LaboratoryResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LaboratoryResult>
 */
class LaboratoryResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LaboratoryResult::class);
    }

    /**
     * @return LaboratoryResult[]
     */
    public function findByTestName(string $testName): array
    {
        return $this->createQueryBuilder('lr')
            ->andWhere('lr.testName LIKE :testName')
            ->andWhere('lr.deletedAt IS NULL')
            ->setParameter('testName', '%' . $testName . '%')
            ->orderBy('lr.measuredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
