<?php

namespace App\Repository\Nutrition;

use App\Entity\Nutrition\Food;
use App\Entity\Nutrition\FoodCategory;
use App\Entity\Healthcare\HealthcareOrganization;
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

    /**
     * Catalogue aliments avec relations hydratées (évite N+1 au warm du cache).
     *
     * @return Food[]
     */
    public function findAllWithCategoryAndCreator(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.category', 'c')->addSelect('c')
            ->leftJoin('f.createdBy', 'cb')->addSelect('cb')
            ->andWhere('f.deletedAt IS NULL')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Food[] */
    public function findByOrganizationWithCategoryAndCreator(HealthcareOrganization $organization): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.category', 'c')->addSelect('c')
            ->leftJoin('f.createdBy', 'cb')->addSelect('cb')
            ->andWhere('f.organization = :organization')
            ->andWhere('f.deletedAt IS NULL')
            ->setParameter('organization', $organization)
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByIdAndOrganization(int $id, HealthcareOrganization $organization): ?Food
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id = :id')
            ->andWhere('f.organization = :organization')
            ->andWhere('f.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
