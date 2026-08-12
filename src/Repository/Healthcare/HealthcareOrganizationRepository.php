<?php

namespace App\Repository\Healthcare;

use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Healthcare\OrganizationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HealthcareOrganization>
 */
class HealthcareOrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HealthcareOrganization::class);
    }

    /**
     * @return HealthcareOrganization[]
     */
    public function findActiveByType(OrganizationType $type): array
    {
        return $this->createQueryBuilder('ho')
            ->andWhere('ho.type = :type')
            ->andWhere('ho.active = :active')
            ->andWhere('ho.deletedAt IS NULL')
            ->setParameter('type', $type)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère une liste paginée des organisations de santé.
     *
     * @return HealthcareOrganization[]
     */
    public function findPaginated(int $page, int $limit): array
    {
        $firstResult = ($page - 1) * $limit;

        return $this->createQueryBuilder('ho')
            ->orderBy('ho.id', 'DESC')
            ->setFirstResult($firstResult)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
