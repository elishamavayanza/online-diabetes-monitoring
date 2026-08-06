<?php

namespace App\Repository\Treatment;

use App\Entity\Treatment\Medication;
use App\Entity\Treatment\MedicationCategory;
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
    public function findByCategory(MedicationCategory $category): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.category = :category')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('category', $category)
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
