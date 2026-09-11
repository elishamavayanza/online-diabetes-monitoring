<?php

namespace App\Repository\Appointment;

use App\Entity\Appointment\Appointment;
use App\Entity\Appointment\AppointmentStatus;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Healthcare\HealthcareFacility;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    /**
     * @return Appointment[]
     */
    public function findByPatientOrderedByScheduledAt(Patient $patient): array
    {
        return $this->createWithJoins()
            ->andWhere('a.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('a.scheduledAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Appointment[]
     */
    public function findByOrganizationOrderedByScheduledAt(HealthcareOrganization $organization): array
    {
        return $this->createWithJoins()
            ->andWhere('a.organization = :organization')
            ->setParameter('organization', $organization)
            ->orderBy('a.scheduledAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Appointment[]
     */
    public function findByProfessionalOrderedByScheduledAt(HealthcareProfessional $professional): array
    {
        return $this->createWithJoins()
            ->andWhere('a.professional = :professional')
            ->setParameter('professional', $professional)
            ->orderBy('a.scheduledAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    private function createWithJoins(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->addSelect('p')->leftJoin('a.patient', 'p')
            ->addSelect('pr')->leftJoin('a.professional', 'pr')
            ->addSelect('o')->leftJoin('a.organization', 'o')
            ->addSelect('f')->leftJoin('a.facility', 'f')
            ->andWhere('a.deletedAt IS NULL');
    }

    /**
     * @return Appointment[]
     */
    public function findUpcomingByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.patient = :patient')
            ->andWhere('a.scheduledAt >= :now')
            ->andWhere('a.status = :status')
            ->andWhere('a.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('status', AppointmentStatus::SCHEDULED)
            ->orderBy('a.scheduledAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Appointment[]
     */
    public function findByProfessionalAndDate(HealthcareProfessional $professional, \DateTimeImmutable $date): array
    {
        $startOfDay = $date->setTime(0, 0, 0);
        $endOfDay = $date->setTime(23, 59, 59);

        return $this->createQueryBuilder('a')
            ->andWhere('a.professional = :professional')
            ->andWhere('a.scheduledAt BETWEEN :start AND :end')
            ->andWhere('a.deletedAt IS NULL')
            ->setParameter('professional', $professional)
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->orderBy('a.scheduledAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
