<?php

namespace App\Repository\Nutrition;

use App\Entity\Nutrition\Meal;
use App\Entity\Nutrition\MealType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Meal>
 */
class MealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meal::class);
    }

    /**
     * @return Meal[]
     */
    public function findByMealType(MealType $mealType): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.mealType = :mealType')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('mealType', $mealType)
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
