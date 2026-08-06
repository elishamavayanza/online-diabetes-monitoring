<?php

namespace App\Repository\Healthcare;

use App\Entity\Healthcare\HealthcareFacility;
use App\Entity\Healthcare\HealthcareOrganization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HealthcareFacility>
 */
class HealthcareFacilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HealthcareFacility::class);
    }

    /**
     * @return HealthcareFacility[]
     */
    public function findByOrganization(HealthcareOrganization $organization): array
    {
        return $this->createQueryBuilder('hf')
            ->andWhere('hf.organization = :organization')
            ->andWhere('hf.deletedAt IS NULL')
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getResult();
    }
}
