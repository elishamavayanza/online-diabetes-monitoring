<?php

namespace App\Repository\Healthcare;

use App\Entity\Healthcare\OrganizationMembership;
use App\Entity\Healthcare\MembershipStatus;
use App\Entity\Identity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrganizationMembership>
 */
class OrganizationMembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationMembership::class);
    }

    /**
     * @return OrganizationMembership[]
     */
    public function findActiveByUser(User $user): array
    {
        return $this->createQueryBuilder('om')
            ->andWhere('om.user = :user')
            ->andWhere('om.status = :status')
            ->andWhere('om.deletedAt IS NULL')
            ->setParameter('user', $user)
            ->setParameter('status', MembershipStatus::ACTIVE)
            ->getQuery()
            ->getResult();
    }
}
