<?php

namespace App\Repository\Treatment;

use App\Entity\Treatment\Medication;
use App\Entity\Treatment\MedicationClass;
use App\Entity\Healthcare\HealthcareOrganization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Medication>
 */
class MedicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medication::class);
    }

    /**
     * @return Medication[]
     */
    public function findByCategory(MedicationClass $category): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.category = :category')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('category', $category)
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Medication[] */
    public function findByOrganization(HealthcareOrganization $organization): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.organization = :organization')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('organization', $organization)
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByIdAndOrganization(string $id, HealthcareOrganization $organization): ?Medication
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :id')
            ->andWhere('m.organization = :organization')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
