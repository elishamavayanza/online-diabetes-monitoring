<?php

namespace App\Repository\Nutrition;

use App\Entity\Identity\Patient;
use App\Entity\Nutrition\Meal;
use App\Entity\Nutrition\MealType;
use DateTimeImmutable;
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

    /**
     * Liste des repas d'un patient avec issuer + items (+ food / createdBy).
     * Les IDs sont d'abord sélectionnés (pour un LIMIT correct), puis hydratés
     * avec les collections — évite le piège Doctrine LIMIT + JOIN collection.
     *
     * @return Meal[]
     */
    public function findByPatientWithIssuerAndItems(
        Patient $patient,
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null,
        ?int $limit = null
    ): array {
        $idsQb = $this->createQueryBuilder('m')
            ->select('m.id')
            ->andWhere('m.patient = :patient')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->orderBy('m.measuredAt', 'DESC');

        if ($from !== null) {
            $idsQb->andWhere('m.measuredAt >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $idsQb->andWhere('m.measuredAt <= :to')->setParameter('to', $to);
        }
        if ($limit !== null && $limit > 0) {
            $idsQb->setMaxResults(min($limit, 2000));
        }

        $ids = array_map(
            static fn (array $row) => $row['id'],
            $idsQb->getQuery()->getScalarResult()
        );

        if ($ids === []) {
            return [];
        }

        $meals = $this->createQueryBuilder('m')
            ->leftJoin('m.issuer', 'issuer')->addSelect('issuer')
            ->leftJoin('m.mealItems', 'mi')->addSelect('mi')
            ->leftJoin('mi.food', 'food')->addSelect('food')
            ->leftJoin('mi.createdBy', 'itemAuthor')->addSelect('itemAuthor')
            ->andWhere('m.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        // Réordonner selon l'ordre des IDs (DESC measuredAt)
        $byId = [];
        foreach ($meals as $meal) {
            $byId[(string) $meal->getId()] = $meal;
        }

        $ordered = [];
        foreach ($ids as $id) {
            $key = (string) $id;
            if (isset($byId[$key])) {
                $ordered[] = $byId[$key];
            }
        }

        return $ordered;
    }
}
