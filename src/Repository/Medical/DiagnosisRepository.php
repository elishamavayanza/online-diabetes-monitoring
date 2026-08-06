<?php

namespace App\Repository\Medical;

use App\Entity\Medical\Diagnosis;
use App\Entity\Identity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Diagnosis>
 */
class DiagnosisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Diagnosis::class);
    }

    /**
     * @return Diagnosis[]
     */
    public function findActiveByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.patient = :patient')
            ->andWhere('d.status = :status')
            ->andWhere('d.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('status', 'ACTIVE')
            ->orderBy('d.diagnosedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
