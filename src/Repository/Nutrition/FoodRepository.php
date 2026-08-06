<?php

namespace App\Repository\Nutrition;

use App\Entity\Nutrition\Food;
use App\Entity\Nutrition\FoodCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Food>
 */
class FoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Food::class);
    }

    /**
     * @return Food[]
     */
    public function findByCategory(FoodCategory $category): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.category = :category')
            ->andWhere('f.deletedAt IS NULL')
            ->setParameter('category', $category)
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
