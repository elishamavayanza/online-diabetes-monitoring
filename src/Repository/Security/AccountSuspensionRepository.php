<?php

namespace App\Repository\Security;

use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\User;
use App\Entity\Security\AccountSuspension;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccountSuspension>
 */
class AccountSuspensionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountSuspension::class);
    }

    private function activeCriteria(string $alias = 's'): array
    {
        return [
            $alias . '.canceledAt IS NULL',
            $alias . '.deletedAt IS NULL',
        ];
    }

    /**
     * Dernière suspension active d'un compte (pour le message de blocage au login).
     */
    public function findLatestActiveForUser(User $user): ?AccountSuspension
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere(...$this->activeCriteria())
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return AccountSuspension[]
     */
    public function findActiveForUser(User $user): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere(...$this->activeCriteria())
            ->orderBy('s.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Dernière suspension active d'une organisation.
     */
    public function findLatestActiveForOrganization(HealthcareOrganization $organization): ?AccountSuspension
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.organization = :organization')
            ->andWhere(...$this->activeCriteria())
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return AccountSuspension[]
     */
    public function findActiveForOrganization(HealthcareOrganization $organization): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.organization = :organization')
            ->andWhere(...$this->activeCriteria())
            ->orderBy('s.createdAt', 'DESC')
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getResult();
    }

    /**
     * Suspensions arrivées à échéance, non levées : à réactiver automatiquement.
     *
     * @return AccountSuspension[]
     */
    public function findExpired(\DateTimeImmutable $now): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.endsAt IS NOT NULL')
            ->andWhere('s.endsAt <= :now')
            ->andWhere(...$this->activeCriteria())
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }
}