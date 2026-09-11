<?php

namespace App\Repository\Medical;

use App\Entity\Identity\Patient;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;

/**
 * Requêtes optimisées pour les mesures patient (JOIN issuer, fenêtre temporelle, limite).
 * Évite le N+1 sur issuer lors de la sérialisation des DTOs.
 */
trait PatientMeasurementQueryTrait
{
    /**
     * @return object[]
     */
    public function findByPatientWithIssuer(
        Patient $patient,
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null,
        ?int $limit = null,
        string $alias = 'm',
        string $dateField = 'measuredAt'
    ): array {
        $qb = $this->createQueryBuilder($alias)
            ->leftJoin(sprintf('%s.issuer', $alias), 'issuer')
            ->addSelect('issuer')
            ->andWhere(sprintf('%s.patient = :patient', $alias))
            ->andWhere(sprintf('%s.deletedAt IS NULL', $alias))
            ->setParameter('patient', $patient)
            ->orderBy(sprintf('%s.%s', $alias, $dateField), 'DESC');

        $this->applyDateWindow($qb, $alias, $dateField, $from, $to);

        if ($limit !== null && $limit > 0) {
            $qb->setMaxResults(min($limit, 2000));
        }

        return $qb->getQuery()->getResult();
    }

    private function applyDateWindow(
        QueryBuilder $qb,
        string $alias,
        string $dateField,
        ?DateTimeImmutable $from,
        ?DateTimeImmutable $to
    ): void {
        if ($from !== null) {
            $qb->andWhere(sprintf('%s.%s >= :from', $alias, $dateField))
                ->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere(sprintf('%s.%s <= :to', $alias, $dateField))
                ->setParameter('to', $to);
        }
    }
}
