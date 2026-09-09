<?php

namespace App\Repository\Notification;

use App\Entity\Notification\ReminderRule;
use App\Entity\Identity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReminderRule>
 */
class ReminderRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReminderRule::class);
    }

    /**
     * @return ReminderRule[]
     */
    public function findActiveByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('rr')
            ->andWhere('rr.patient = :patient')
            ->andWhere('rr.active = :active')
            ->andWhere('rr.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ReminderRule[]
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('rr')
            ->andWhere('rr.active = :active')
            ->andWhere('rr.deletedAt IS NULL')
            ->setParameter('active', true)
            ->orderBy('rr.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
