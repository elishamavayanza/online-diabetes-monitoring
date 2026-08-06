<?php

namespace App\Repository\Patient;

use App\Entity\Patient\EmergencyContact;
use App\Entity\Identity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmergencyContact>
 */
class EmergencyContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmergencyContact::class);
    }

    /**
     * @return EmergencyContact[]
     */
    public function findByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('ec')
            ->andWhere('ec.patient = :patient')
            ->andWhere('ec.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->getQuery()
            ->getResult();
    }
}
