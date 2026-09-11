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
            ->leftJoin('pi.medication', 'med')->addSelect('med')
            ->leftJoin('med.insulins', 'ins')->addSelect('ins')
            ->leftJoin('pi.createdBy', 'cb')->addSelect('cb')
            ->andWhere('pi.prescription = :prescription')
            ->andWhere('pi.deletedAt IS NULL')
            ->setParameter('prescription', $prescription)
            ->getQuery()
            ->getResult();
    }

    /**
     * Charge tous les items pour un ensemble de prescriptions (évite le waterfall N requêtes).
     *
     * @param array<int|string> $prescriptionIds
     * @return PrescriptionItem[]
     */
    public function findByPrescriptionIds(array $prescriptionIds): array
    {
        if ($prescriptionIds === []) {
            return [];
        }

        return $this->createQueryBuilder('pi')
            ->leftJoin('pi.createdBy', 'cb')->addSelect('cb')
            ->leftJoin('pi.medication', 'med')->addSelect('med')
            ->leftJoin('med.insulins', 'ins')->addSelect('ins')
            ->andWhere('pi.prescription IN (:ids)')
            ->andWhere('pi.deletedAt IS NULL')
            ->setParameter('ids', $prescriptionIds)
            ->getQuery()
            ->getResult();
    }
}
