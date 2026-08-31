<?php

namespace App\Repository\Communication;

use App\Entity\Communication\Conversation;
use App\Entity\Identity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    /**
     * @return Conversation[]
     */
    public function findByPatientUser(string $patientUserId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.patient = :patient')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('patient', $patientUserId)
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string[] $patientUserIds
     * @return Conversation[]
     */
    public function findByPatientUserIds(array $patientUserIds): array
    {
        if ($patientUserIds === []) {
            return [];
        }

        return $this->createQueryBuilder('c')
            ->andWhere('c.patient IN (:patients)')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('patients', $patientUserIds)
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLatestOpenByPatient(string $patientUserId): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.patient = :patient')
            ->andWhere('c.closedAt IS NULL')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('patient', $patientUserId)
            ->orderBy('c.updatedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Conversation[]
     * @deprecated Utiliser findByPatientUserIds à la place.
     */
    public function findActiveByUser(User $user): array
    {
        return $this->findByPatientUser((string) $user->getId());
    }
}
