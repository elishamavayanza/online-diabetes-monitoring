<?php

namespace App\Repository\Common;

use App\Entity\Common\FileAttachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<FileAttachment>
 */
class FileAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileAttachment::class);
    }

    /**
     * @return FileAttachment[]
     */
    public function findByEntity(string $entityType, Uuid $entityId): array
    {
        return $this->createQueryBuilder('fa')
            ->andWhere('fa.entityType = :entityType')
            ->andWhere('fa.entityId = :entityId')
            ->setParameter('entityType', $entityType)
            ->setParameter('entityId', $entityId->toBinary())
            ->getQuery()
            ->getResult();
    }
}
