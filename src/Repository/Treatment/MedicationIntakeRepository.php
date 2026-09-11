<?php

namespace App\Repository\Treatment;

use App\Entity\Treatment\MedicationIntake;
use App\Entity\Treatment\IntakeStatus;
use App\Entity\Treatment\PrescriptionItem;
use App\Entity\Identity\Patient;
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

    /**
     * Charge toutes les prises d'un patient en une seule requête, avec les
     * associations nécessaires à la sérialisation (évite le findAll() global
     * et le N+1 sur l'émetteur).
     *
     * @return MedicationIntake[]
     */
    public function findForPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('mi')
            ->leftJoin('mi.prescriptionItem', 'pitem')->addSelect('pitem')
            ->leftJoin('mi.patient', 'p')->addSelect('p')
            ->leftJoin('mi.issuer', 'issuer')->addSelect('issuer')
            ->andWhere('mi.patient = :patient')
            ->andWhere('mi.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->orderBy('mi.takenAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
