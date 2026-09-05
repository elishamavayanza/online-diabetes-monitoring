<?php

namespace App\Repository\Healthcare;

use App\Entity\Identity\HealthcareProfessional;
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
}
