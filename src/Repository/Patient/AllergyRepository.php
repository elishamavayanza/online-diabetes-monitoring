<?php

namespace App\Repository\Patient;

use App\Entity\Patient\Allergy;
use App\Entity\Patient\AllergySeverity;
use App\Entity\Identity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Allergy>
 */
class AllergyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Allergy::class);
    }

    /**
     * @return Allergy[]
     */
    public function findByPatientAndSeverity(Patient $patient, AllergySeverity $severity): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.patient = :patient')
            ->andWhere('a.severity = :severity')
            ->andWhere('a.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('severity', $severity)
            ->orderBy('a.diagnosedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
