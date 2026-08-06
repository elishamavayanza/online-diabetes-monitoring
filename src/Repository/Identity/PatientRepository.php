<?php

namespace App\Repository\Identity;

use App\Entity\Identity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Patient>
 */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

//    /**
//     * @return Patient[]
//     */
//subFindActivePatients(): array
//{
//    // non-deleted patients
//}

public function findByBloodType(string $bloodType): array
{
    return $this->createQueryBuilder('p')
        ->andWhere('p.bloodType = :bloodType')
        ->andWhere('p.deletedAt IS NULL')
        ->setParameter('bloodType', $bloodType)
        ->getQuery()
        ->getResult();
}

public function searchByNameQuery(string $query): array
{
    return $this->createQueryBuilder('p')
        ->andWhere('p.firstName LIKE :query OR p.lastName LIKE :query')
        ->andWhere('p.deletedAt IS NULL')
        ->setParameter('query', '%' . $query . '%')
        ->getQuery()
        ->getResult();
}
}
