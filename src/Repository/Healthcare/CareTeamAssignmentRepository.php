<?php

namespace App\Repository\Healthcare;

use App\Entity\Healthcare\CareTeamAssignment;
use App\Entity\Healthcare\CareTeamRole;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CareTeamAssignment>
 */
class CareTeamAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CareTeamAssignment::class);
    }

    /**
     * @return CareTeamAssignment[]
     */
    public function findActiveByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('cta')
            ->andWhere('cta.patient = :patient')
            ->andWhere('cta.active = :active')
            ->andWhere('cta.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return CareTeamAssignment[]
     */
    public function findByProfessional(HealthcareProfessional $professional): array
    {
        return $this->createQueryBuilder('cta')
            ->andWhere('cta.professional = :professional')
            ->andWhere('cta.deletedAt IS NULL')
            ->setParameter('professional', $professional)
            ->getQuery()
            ->getResult();
    }

    /** @return CareTeamAssignment[] */
    public function findByOrganization(HealthcareOrganization $organization): array
    {
        return $this->findBy(['organization' => $organization], ['createdAt' => 'DESC']);
    }

    public function hasActiveAssignment(
        Patient $patient,
        HealthcareProfessional $professional,
        HealthcareOrganization $organization,
        CareTeamRole $role,
        ?string $excludedAssignmentId = null
    ): bool {
        $query = $this->createQueryBuilder('assignment')
            ->select('COUNT(assignment.id)')
            ->andWhere('assignment.patient = :patient')
            ->andWhere('assignment.professional = :professional')
            ->andWhere('assignment.organization = :organization')
            ->andWhere('assignment.role = :role')
            ->andWhere('assignment.active = :active')
            ->setParameter('patient', $patient)
            ->setParameter('professional', $professional)
            ->setParameter('organization', $organization)
            ->setParameter('role', $role)
            ->setParameter('active', true);

        if ($excludedAssignmentId !== null) {
            $query
                ->andWhere('assignment.id != :excludedAssignmentId')
                ->setParameter('excludedAssignmentId', $excludedAssignmentId);
        }

        return (int) $query->getQuery()->getSingleScalarResult() > 0;
    }

    public function isUserActivelyAssignedToPatient(string $userId, Patient $patient): bool
    {
        return (int) $this->createQueryBuilder('assignment')
            ->select('COUNT(assignment.id)')
            ->andWhere('assignment.patient = :patient')
            ->andWhere('IDENTITY(assignment.professional) = :userId')
            ->andWhere('assignment.active = :active')
            ->setParameter('patient', $patient)
            ->setParameter('userId', $userId)
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}
