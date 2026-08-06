<?php

namespace App\Repository\Treatment;

use App\Entity\Treatment\PrescriptionVersion;
use App\Entity\Treatment\Prescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrescriptionVersion>
 */
class PrescriptionVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrescriptionVersion::class);
    }

    /**
     * @return PrescriptionVersion[]
     */
    public function findByPrescription(Prescription $prescription): array
    {
        return $this->createQueryBuilder('pv')
            ->andWhere('pv.prescription = :prescription')
            ->andWhere('pv.deletedAt IS NULL')
            ->setParameter('prescription', $prescription)
            ->orderBy('pv.versionNumber', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
