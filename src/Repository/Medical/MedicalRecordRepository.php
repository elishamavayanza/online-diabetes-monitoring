<?php

namespace App\Repository\Medical;

use App\Entity\Medical\MedicalRecord;
use App\Entity\Medical\MedicalRecordStatus;
use App\Entity\Identity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicalRecord>
 */
class MedicalRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalRecord::class);
    }

    public function findOpenRecordForPatient(Patient $patient): ?MedicalRecord
    {
        return $this->createQueryBuilder('mr')
            ->andWhere('mr.patient = :patient')
            ->andWhere('mr.status = :status')
            ->andWhere('mr.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('status', MedicalRecordStatus::OPEN)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
