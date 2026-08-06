<?php

namespace App\Repository\Patient;

use App\Entity\Patient\MedicalConsent;
use App\Entity\Patient\ConsentType;
use App\Entity\Identity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicalConsent>
 */
class MedicalConsentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalConsent::class);
    }

    /**
     * @return MedicalConsent[]
     */
    public function findActiveByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('mc')
            ->andWhere('mc.patient = :patient')
            ->andWhere('mc.revokedAt IS NULL')
            ->andWhere('mc.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->orderBy('mc.grantedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
