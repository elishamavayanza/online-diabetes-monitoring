<?php

namespace App\Repository\Healthcare;

use App\Entity\Healthcare\CareTeamAssignment;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CareTeamAssignment>
 */
class CareTeamAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CareTeamAssignment::class);
    }

    /**
     * @return CareTeamAssignment[]
     */
    public function findActiveByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('cta')
            ->andWhere('cta.patient = :patient')
            ->andWhere('cta.active = :active')
            ->andWhere('cta.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return CareTeamAssignment[]
     */
    public function findByProfessional(HealthcareProfessional $professional): array
    {
        return $this->createQueryBuilder('cta')
            ->andWhere('cta.professional = :professional')
            ->andWhere('cta.deletedAt IS NULL')
            ->setParameter('professional', $professional)
            ->getQuery()
            ->getResult();
    }
}
