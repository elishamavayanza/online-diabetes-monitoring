<?php

namespace App\Repository\Treatment;

use App\Entity\Treatment\Insulin;
use App\Entity\Treatment\Medication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Insulin>
 */
class InsulinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Insulin::class);
    }

    /**
     * @return Insulin[]
     */
    public function findByMedication(Medication $medication): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.medication = :medication')
            ->andWhere('i.deletedAt IS NULL')
            ->setParameter('medication', $medication)
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}