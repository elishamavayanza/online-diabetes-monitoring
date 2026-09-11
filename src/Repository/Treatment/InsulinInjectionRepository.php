<?php

namespace App\Repository\Treatment;

use App\Entity\Identity\Patient;
use App\Entity\Treatment\InsulinInjection;
use App\Entity\Treatment\PrescriptionItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InsulinInjection>
 */
class InsulinInjectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InsulinInjection::class);
    }

    /**
     * @return InsulinInjection[]
     */
    public function findByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('ii')
            ->leftJoin('ii.prescriptionItem', 'pitem')->addSelect('pitem')
            ->leftJoin('ii.insulin', 'ins')->addSelect('ins')
            ->leftJoin('ii.issuer', 'issuer')->addSelect('issuer')
            ->andWhere('ii.patient = :patient')
            ->andWhere('ii.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->orderBy('ii.injectedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return InsulinInjection[]
     */
    public function findByPrescriptionItem(PrescriptionItem $prescriptionItem): array
    {
        return $this->createQueryBuilder('ii')
            ->leftJoin('ii.prescriptionItem', 'pitem')->addSelect('pitem')
            ->leftJoin('ii.insulin', 'ins')->addSelect('ins')
            ->leftJoin('ii.issuer', 'issuer')->addSelect('issuer')
            ->andWhere('ii.prescriptionItem = :prescriptionItem')
            ->andWhere('ii.deletedAt IS NULL')
            ->setParameter('prescriptionItem', $prescriptionItem)
            ->orderBy('ii.injectedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}