<?php

namespace App\Repository\Common;

use App\Entity\Common\PatientCommonOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PatientCommonOperation>
 */
class PatientCommonOperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientCommonOperation::class);
    }

}
