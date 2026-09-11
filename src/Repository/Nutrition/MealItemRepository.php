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
            ->leftJoin('mi.food', 'f')->addSelect('f')
            ->leftJoin('mi.createdBy', 'cb')->addSelect('cb')
            ->andWhere('mi.meal = :meal')
            ->andWhere('mi.deletedAt IS NULL')
            ->setParameter('meal', $meal)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return MealItem[]
     */
    public function findByPatient(int $patientId): array
    {
        return $this->createQueryBuilder('mi')
            ->leftJoin('mi.meal', 'm')->addSelect('m')
            ->leftJoin('mi.food', 'f')->addSelect('f')
            ->leftJoin('mi.createdBy', 'cb')->addSelect('cb')
            ->where('m.patient = :patientId')
            ->andWhere('mi.deletedAt IS NULL')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('patientId', $patientId)
            ->orderBy('mi.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
