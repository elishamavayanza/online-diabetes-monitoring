<?php

namespace App\Repository\Communication;

use App\Entity\Communication\MessageReadReceipt;
use App\Entity\Communication\Message;
use App\Entity\Communication\ConversationParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessageReadReceipt>
 */
class MessageReadReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageReadReceipt::class);
    }

    public function findByMessageAndParticipant(Message $message, ConversationParticipant $participant): ?MessageReadReceipt
    {
        return $this->createQueryBuilder('mrr')
            ->andWhere('mrr.message = :message')
            ->andWhere('mrr.participant = :participant')
            ->andWhere('mrr.deletedAt IS NULL')
            ->setParameter('message', $message)
            ->setParameter('participant', $participant)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
