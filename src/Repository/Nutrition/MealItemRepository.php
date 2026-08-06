<?php

namespace App\Repository\Nutrition;

use App\Entity\Nutrition\MealItem;
use App\Entity\Nutrition\Meal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MealItem>
 */
class MealItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MealItem::class);
    }

    /**
     * @return MealItem[]
     */
    public function findByMeal(Meal $meal): array
    {
        return $this->createQueryBuilder('mi')
            ->andWhere('mi.meal = :meal')
            ->andWhere('mi.deletedAt IS NULL')
            ->setParameter('meal', $meal)
            ->getQuery()
            ->getResult();
    }
}
