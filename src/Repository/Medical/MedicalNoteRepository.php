<?php

namespace App\Repository\Medical;

use App\Entity\Medical\MedicalNote;
use App\Entity\Medical\MedicalRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicalNote>
 */
class MedicalNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalNote::class);
    }

    /**
     * @return MedicalNote[]
     */
    public function findByMedicalRecord(MedicalRecord $medicalRecord): array
    {
        return $this->createQueryBuilder('mn')
            ->andWhere('mn.medicalRecord = :medicalRecord')
            ->andWhere('mn.deletedAt IS NULL')
            ->setParameter('medicalRecord', $medicalRecord)
            ->orderBy('mn.notedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
