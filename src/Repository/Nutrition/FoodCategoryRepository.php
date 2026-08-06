<?php

namespace App\Repository\Nutrition;

use App\Entity\Nutrition\FoodCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FoodCategory>
 */
class FoodCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodCategory::class);
    }

    /**
     * @return FoodCategory[]
     */
    public function findAllOrderedByLabel(): array
    {
        return $this->createQueryBuilder('fc')
            ->andWhere('fc.deletedAt IS NULL')
            ->orderBy('fc.label', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
