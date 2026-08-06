<?php

namespace App\Repository\Treatment;

use App\Entity\Treatment\PrescriptionItem;
use App\Entity\Treatment\Prescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrescriptionItem>
 */
class PrescriptionItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrescriptionItem::class);
    }

    /**
     * @return PrescriptionItem[]
     */
    public function findByPrescription(Prescription $prescription): array
    {
        return $this->createQueryBuilder('pi')
            ->andWhere('pi.prescription = :prescription')
            ->andWhere('pi.deletedAt IS NULL')
            ->setParameter('prescription', $prescription)
            ->getQuery()
            ->getResult();
    }
}
