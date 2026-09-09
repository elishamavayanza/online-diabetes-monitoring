<?php

namespace App\Repository\Communication;

use App\Entity\Communication\Message;
use App\Entity\Communication\Conversation;
use App\Entity\Communication\MessageAttachment;
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
            ->addSelect('s')
            ->join('m.sender', 's')
            ->andWhere('m.conversation = :conversation')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('conversation', $conversation)
            ->orderBy('m.sentAt', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Pré-charge toutes les pièces jointes des messages fournis en une seule
     * requête, groupées par id de message (évite le N+1 des listes).
     *
     * @param Message[] $messages
     *
     * @return array<int|string, MessageAttachment[]>
     */
    public function findAttachmentsByMessages(array $messages): array
    {
        if ($messages === []) {
            return [];
        }

        $ids = array_map(static fn (Message $m): mixed => $m->getId(), $messages);

        $attachments = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from(MessageAttachment::class, 'a')
            ->where('a.message IN (:ids)')
            ->andWhere('a.deletedAt IS NULL')
            ->setParameter('ids', $ids)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();

        $grouped = [];
        foreach ($attachments as $attachment) {
            $grouped[(string) $attachment->getMessage()?->getId()][] = $attachment;
        }

        return $grouped;
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

    /**
     * Dernier message ({id, content, sentAt}) de chaque conversation fournie,
     * en une seule requête (évite le chargement de toute la collection dans
     * ConversationQueryService::getMine).
     *
     * @param Conversation[] $conversations
     *
     * @return array<string, array{message: Message|null}>
     */
    public function findLastMessageByConversations(array $conversations): array
    {
        if ($conversations === []) {
            return [];
        }

        $conversationIds = array_map(
            static fn (Conversation $c): mixed => $c->getId(),
            $conversations
        );

        $messages = $this->createQueryBuilder('m')
            ->select('m')
            ->andWhere('m.conversation IN (:conversationIds)')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('conversationIds', $conversationIds)
            ->orderBy('m.sentAt', 'ASC')
            ->getQuery()
            ->getResult();

        $lastByConversation = [];
        foreach ($messages as $message) {
            $key = (string) $message->getConversation()?->getId();
            $lastByConversation[$key] = $message;
        }

        // Garantit une entrée même sans message.
        foreach ($conversations as $conversation) {
            $key = (string) $conversation->getId();
            if (!isset($lastByConversation[$key])) {
                $lastByConversation[$key] = null;
            }
        }

        return $lastByConversation;
    }

    /**
     * Nombre de messages non lus pour un utilisateur, par conversation, en une
     * seule requête groupée.
     *
     * @param Conversation[] $conversations
     *
     * @return array<string, int>
     */
    public function countUnreadByConversation(array $conversations, User $user): array
    {
        if ($conversations === []) {
            return [];
        }

        $conversationIds = array_map(
            static fn (Conversation $c): mixed => $c->getId(),
            $conversations
        );

        $rows = $this->createQueryBuilder('m')
            ->select('IDENTITY(m.conversation) AS convId, COUNT(m.id) AS cnt')
            ->leftJoin(
                MessageReadReceipt::class,
                'r',
                'WITH',
                'r.message = m AND r.user = :user AND r.deletedAt IS NULL'
            )
            ->where('m.conversation IN (:conversationIds)')
            ->andWhere('m.sender != :user')
            ->andWhere('m.deletedAt IS NULL')
            ->andWhere('r.id IS NULL')
            ->groupBy('convId')
            ->setParameter('conversationIds', $conversationIds)
            ->setParameter('user', $user)
            ->getQuery()
            ->getArrayResult();

        $counts = [];
        foreach ($conversations as $conversation) {
            $counts[(string) $conversation->getId()] = 0;
        }
        foreach ($rows as $row) {
            $counts[(string) $row['convId']] = (int) $row['cnt'];
        }

        return $counts;
    }
}
