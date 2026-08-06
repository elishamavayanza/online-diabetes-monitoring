<?php

namespace App\Repository\Treatment;

use App\Entity\Treatment\Prescription;
use App\Entity\Treatment\PrescriptionStatus;
use App\Entity\Identity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prescription>
 */
class PrescriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prescription::class);
    }

    /**
     * @return Prescription[]
     */
    public function findActiveByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.patient = :patient')
            ->andWhere('p.status = :status')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('status', PrescriptionStatus::ACTIVE)
            ->orderBy('p.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
