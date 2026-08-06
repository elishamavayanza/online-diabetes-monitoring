<?php

namespace App\Repository\Audit;

use App\Entity\Audit\DataAccessLog;
use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DataAccessLog>
 */
class DataAccessLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataAccessLog::class);
    }

    /**
     * @return DataAccessLog[]
     */
    public function findByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('dal')
            ->andWhere('dal.patient = :patient')
            ->andWhere('dal.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->orderBy('dal.accessedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return DataAccessLog[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('dal')
            ->andWhere('dal.accessedBy = :user')
            ->andWhere('dal.deletedAt IS NULL')
            ->setParameter('user', $user)
            ->orderBy('dal.accessedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
