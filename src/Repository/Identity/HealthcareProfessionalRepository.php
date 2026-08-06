<?php

namespace App\Repository\Identity;

use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Identity\ProfessionalType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HealthcareProfessional>
 */
class HealthcareProfessionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HealthcareProfessional::class);
    }

    /**
     * @return HealthcareProfessional[]
     */
    public function findByTypeAndSpecialty(ProfessionalType $type, ?string $specialty = null): array
    {
        $qb = $this->createQueryBuilder('hp')
            ->andWhere('hp.professionalType = :type')
            ->andWhere('hp.deletedAt IS NULL')
            ->setParameter('type', $type);

        if ($specialty !== null) {
            $qb->andWhere('hp.specialty LIKE :specialty')
                ->setParameter('specialty', '%' . $specialty . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function findByLicenseNumber(string $licenseNumber): ?HealthcareProfessional
    {
        return $this->createQueryBuilder('hp')
            ->andWhere('hp.licenseNumber = :licenseNumber')
            ->andWhere('hp.deletedAt IS NULL')
            ->setParameter('licenseNumber', $licenseNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
