<?php

namespace App\Repository\Communication;

use App\Entity\Communication\MessageAttachment;
use App\Entity\Communication\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessageAttachment>
 */
class MessageAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageAttachment::class);
    }

    /**
     * @return MessageAttachment[]
     */
    public function findByMessage(Message $message): array
    {
        return $this->createQueryBuilder('ma')
            ->andWhere('ma.message = :message')
            ->andWhere('ma.deletedAt IS NULL')
            ->setParameter('message', $message)
            ->getQuery()
            ->getResult();
    }
}
