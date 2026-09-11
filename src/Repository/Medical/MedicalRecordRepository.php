<?php

namespace App\Repository\Medical;

use App\Entity\Medical\MedicalRecord;
use App\Entity\Medical\MedicalRecordStatus;
use App\Entity\Identity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicalRecord>
 */
class MedicalRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalRecord::class);
    }

    public function findOpenRecordForPatient(Patient $patient): ?MedicalRecord
    {
        return $this->createQueryBuilder('mr')
            ->andWhere('mr.patient = :patient')
            ->andWhere('mr.status = :status')
            ->andWhere('mr.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('status', MedicalRecordStatus::OPEN)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLatestRecordForPatient(Patient $patient): ?MedicalRecord
    {
        return $this->createQueryBuilder('mr')
            ->andWhere('mr.patient = :patient')
            ->andWhere('mr.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->orderBy('mr.openedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Retourne le statut de dossier médical pour un ensemble de patients en une
     * seule requête (évite le N+1 HTTP côté front). Un dossier OPEN prime sur CLOSED.
     *
     * @param array<int|string> $patientIds
     * @return array<string, MedicalRecordStatus> map patientId -> statut
     */
    public function findStatusesByPatientIds(array $patientIds): array
    {
        if ($patientIds === []) {
            return [];
        }

        $rows = $this->createQueryBuilder('mr')
            ->select('IDENTITY(mr.patient) AS patientId', 'mr.status')
            ->andWhere('mr.patient IN (:patients)')
            ->andWhere('mr.deletedAt IS NULL')
            ->setParameter('patients', $patientIds)
            ->getQuery()
            ->getResult();

        $statuses = [];
        foreach ($rows as $row) {
            $patientId = (string) $row['patientId'];
            $status = $row['status'];
            if ($status === null) {
                continue;
            }
            if (!isset($statuses[$patientId]) || $statuses[$patientId] === MedicalRecordStatus::CLOSED) {
                $statuses[$patientId] = $status;
            }
        }

        return $statuses;
    }
}
