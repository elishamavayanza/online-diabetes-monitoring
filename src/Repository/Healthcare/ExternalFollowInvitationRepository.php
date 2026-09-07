<?php

namespace App\Repository\Healthcare;

use App\Entity\Healthcare\ExternalFollowInvitation;
use App\Entity\Healthcare\ExternalFollowStatus;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\HealthcareProfessional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExternalFollowInvitation>
 */
class ExternalFollowInvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalFollowInvitation::class);
    }

    /**
     * @return ExternalFollowInvitation[]
     */
    public function findByOrganization(
        HealthcareOrganization $organization,
        bool $includeExpired = true
    ): array {
        $today = new \DateTimeImmutable('today');

        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.organization = :organization')
            ->andWhere('i.deletedAt IS NULL')
            ->setParameter('organization', $organization)
            ->orderBy('i.createdAt', 'DESC');

        if (!$includeExpired) {
            $qb
                ->andWhere('i.status IN (:pending, :accepted)')
                ->andWhere('(i.endDate IS NULL OR i.endDate >= :today)')
                ->setParameter('pending', ExternalFollowStatus::PENDING)
                ->setParameter('accepted', ExternalFollowStatus::ACCEPTED)
                ->setParameter('today', $today);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByToken(string $token): ?ExternalFollowInvitation
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.token = :token')
            ->andWhere('i.deletedAt IS NULL')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return ExternalFollowInvitation[]
     */
    public function findActiveByProfessional(HealthcareProfessional $professional): array
    {
        $today = new \DateTimeImmutable('today');

        return $this->createQueryBuilder('i')
            ->andWhere('i.professional = :professional')
            ->andWhere('i.status = :accepted')
            ->andWhere('(i.endDate IS NULL OR i.endDate >= :today)')
            ->andWhere('i.deletedAt IS NULL')
            ->setParameter('professional', $professional)
            ->setParameter('accepted', ExternalFollowStatus::ACCEPTED)
            ->setParameter('today', $today)
            ->orderBy('i.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingBetween(
        HealthcareOrganization $organization,
        \App\Entity\Identity\Patient $patient,
        HealthcareProfessional $professional
    ): ?ExternalFollowInvitation {
        return $this->createQueryBuilder('i')
            ->andWhere('i.organization = :organization')
            ->andWhere('i.patient = :patient')
            ->andWhere('i.professional = :professional')
            ->andWhere('i.status = :pending')
            ->andWhere('i.deletedAt IS NULL')
            ->setParameter('organization', $organization)
            ->setParameter('patient', $patient)
            ->setParameter('professional', $professional)
            ->setParameter('pending', ExternalFollowStatus::PENDING)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}