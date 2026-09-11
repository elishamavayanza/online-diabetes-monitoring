<?php

namespace App\Repository\Communication;

use App\Entity\Communication\MessageReadReceipt;
use App\Entity\Communication\Message;
use App\Entity\Identity\User;
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

    public function findByMessageAndUser(Message $message, User $user): ?MessageReadReceipt
    {
        return $this->createQueryBuilder('mrr')
            ->andWhere('mrr.message = :message')
            ->andWhere('mrr.user = :user')
            ->andWhere('mrr.deletedAt IS NULL')
            ->setParameter('message', $message)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findReadReceiptForMessageAndUser(Message $message, User $reader): ?MessageReadReceipt
    {
        return $this->findByMessageAndUser($message, $reader);
    }

    /**
     * @return MessageReadReceipt[]
     */
    public function findReadReceiptsForMessage(Message $message): array
    {
        return $this->createQueryBuilder('mrr')
            ->andWhere('mrr.message = :message')
            ->andWhere('mrr.deletedAt IS NULL')
            ->setParameter('message', $message)
            ->getQuery()
            ->getResult();
    }

    /**
     * Pré-charge les accusés de lecture de tous les messages fournis en une
     * seule requête, groupés par id de message (évite le N+1 des listes).
     *
     * @param Message[] $messages
     *
     * @return array<int|string, MessageReadReceipt[]>
     */
    public function findReceiptsByMessages(array $messages): array
    {
        if ($messages === []) {
            return [];
        }

        $ids = array_map(static fn (Message $m): mixed => $m->getId(), $messages);

        $receipts = $this->createQueryBuilder('mrr')
            ->addSelect('u')
            ->join('mrr.user', 'u')
            ->where('mrr.message IN (:ids)')
            ->andWhere('mrr.deletedAt IS NULL')
            ->setParameter('ids', $ids)
            ->orderBy('mrr.readAt', 'ASC')
            ->getQuery()
            ->getResult();

        $grouped = [];
        foreach ($receipts as $receipt) {
            $grouped[(string) $receipt->getMessage()?->getId()][] = $receipt;
        }

        return $grouped;
    }
}
