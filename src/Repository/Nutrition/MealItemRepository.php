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
            ->setParameter('meal', $meal)
            ->getQuery()
            ->getResult();
    }

    public function findByPatient(int $patientId): array
    {
        return $this->createQueryBuilder('mi')
            ->join('mi.meal', 'm')
            ->where('m.patient = :patientId')
            ->setParameter('patientId', $patientId)
            ->getQuery()
            ->getResult();
    }
}
