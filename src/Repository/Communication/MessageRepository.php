<?php

namespace App\Repository\Communication;

use App\Entity\Communication\Message;
use App\Entity\Communication\Conversation;
use App\Entity\Communication\MessageReadReceipt;
use App\Entity\Identity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return Message[]
     */
    public function findByConversationOrderedAsc(Conversation $conversation, int $limit = 100): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.conversation = :conversation')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('conversation', $conversation)
            ->orderBy('m.sentAt', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countUnreadForUser(Conversation $conversation, User $user): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->leftJoin(
                MessageReadReceipt::class,
                'r',
                'WITH',
                'r.message = m AND r.user = :user AND r.deletedAt IS NULL'
            )
            ->andWhere('m.conversation = :conversation')
            ->andWhere('m.sender != :user')
            ->andWhere('m.deletedAt IS NULL')
            ->andWhere('r.id IS NULL')
            ->setParameter('conversation', $conversation)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Message[]
     */
    public function findUnreadIncomingForUser(Conversation $conversation, User $user): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin(
                MessageReadReceipt::class,
                'r',
                'WITH',
                'r.message = m AND r.user = :user AND r.deletedAt IS NULL'
            )
            ->andWhere('m.conversation = :conversation')
            ->andWhere('m.sender != :user')
            ->andWhere('m.deletedAt IS NULL')
            ->andWhere('r.id IS NULL')
            ->setParameter('conversation', $conversation)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
