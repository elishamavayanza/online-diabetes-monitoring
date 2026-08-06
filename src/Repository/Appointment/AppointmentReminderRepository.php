<?php

namespace App\Repository\Appointment;

use App\Entity\Appointment\AppointmentReminder;
use App\Entity\Appointment\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AppointmentReminder>
 */
class AppointmentReminderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppointmentReminder::class);
    }

    /**
     * @return AppointmentReminder[]
     */
    public function findPendingReminders(\DateTimeImmutable $now): array
    {
        return $this->createQueryBuilder('ar')
            ->andWhere('ar.scheduledFor <= :now')
            ->andWhere('ar.sentAt IS NULL')
            ->andWhere('ar.deletedAt IS NULL')
            ->setParameter('now', $now)
            ->orderBy('ar.scheduledFor', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return AppointmentReminder[]
     */
    public function findByAppointment(Appointment $appointment): array
    {
        return $this->createQueryBuilder('ar')
            ->andWhere('ar.appointment = :appointment')
            ->andWhere('ar.deletedAt IS NULL')
            ->setParameter('appointment', $appointment)
            ->orderBy('ar.scheduledFor', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
