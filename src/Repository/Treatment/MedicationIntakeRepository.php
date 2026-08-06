<?php

namespace App\Repository\Treatment;

use App\Entity\Treatment\MedicationIntake;
use App\Entity\Treatment\IntakeStatus;
use App\Entity\Treatment\PrescriptionItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicationIntake>
 */
class MedicationIntakeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicationIntake::class);
    }

    /**
     * @return MedicationIntake[]
     */
    public function findByPrescriptionItem(PrescriptionItem $prescriptionItem): array
    {
        return $this->createQueryBuilder('mi')
            ->andWhere('mi.prescriptionItem = :prescriptionItem')
            ->andWhere('mi.deletedAt IS NULL')
            ->setParameter('prescriptionItem', $prescriptionItem)
            ->orderBy('mi.takenAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
