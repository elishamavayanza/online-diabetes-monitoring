<?php

namespace App\Repository\Communication;

use App\Entity\Communication\ConversationParticipant;
use App\Entity\Communication\Conversation;
use App\Entity\Identity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConversationParticipant>
 */
class ConversationParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConversationParticipant::class);
    }

    public function findActiveParticipant(Conversation $conversation, User $user): ?ConversationParticipant
    {
        return $this->createQueryBuilder('cp')
            ->andWhere('cp.conversation = :conversation')
            ->andWhere('cp.user = :user')
            ->andWhere('cp.leftAt IS NULL')
            ->andWhere('cp.deletedAt IS NULL')
            ->setParameter('conversation', $conversation)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
