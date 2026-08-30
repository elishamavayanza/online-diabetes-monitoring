<?php

namespace App\Repository\Healthcare;

use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Healthcare\MembershipStatus;
use App\Entity\Healthcare\OrganizationMembership;
use App\Entity\Identity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrganizationMembership>
 */
class OrganizationMembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationMembership::class);
    }

    /**
     * @return OrganizationMembership[]
     */
    public function findActiveByUser(\App\Entity\Identity\User $user): array
    {
        return $this->createQueryBuilder('om')
            ->andWhere('om.user = :user')
            ->andWhere('om.status = :status')
            ->andWhere('om.deletedAt IS NULL')
            ->setParameter('user', $user)
            ->setParameter('status', MembershipStatus::ACTIVE)
            ->getQuery()
            ->getResult();
    }

  /**
   * @return int[]
   */
  public function findPatientIdsByOrganization(HealthcareOrganization $organization): array
  {
    $result = $this->createQueryBuilder('om')
      ->select('IDENTITY(om.user)')
      ->innerJoin('om.user', 'u')
      ->andWhere('om.organization = :organization')
      ->andWhere('om.status = :status')
      ->andWhere('om.deletedAt IS NULL')
      ->andWhere('u INSTANCE OF ' . Patient::class)
      ->setParameter('organization', $organization)
      ->setParameter('status', MembershipStatus::ACTIVE)
      ->getQuery()
      ->getSingleColumnResult();

    return array_map('intval', $result);
  }

  public function countPatientsByOrganization(HealthcareOrganization $organization): int
  {
    return (int) $this->createQueryBuilder('om')
      ->select('COUNT(om.id)')
      ->innerJoin('om.user', 'u')
      ->andWhere('om.organization = :organization')
      ->andWhere('om.status = :status')
      ->andWhere('om.deletedAt IS NULL')
      ->andWhere('u INSTANCE OF ' . Patient::class)
      ->setParameter('organization', $organization)
      ->setParameter('status', MembershipStatus::ACTIVE)
      ->getQuery()
      ->getSingleScalarResult();
  }

  public function countNewPatientsInPeriod(
    HealthcareOrganization $organization,
    \DateTimeImmutable $from,
    \DateTimeImmutable $to
  ): int {
    return (int) $this->createQueryBuilder('om')
      ->select('COUNT(om.id)')
      ->innerJoin('om.user', 'u')
      ->andWhere('om.organization = :organization')
      ->andWhere('om.status = :status')
      ->andWhere('om.deletedAt IS NULL')
      ->andWhere('u INSTANCE OF ' . Patient::class)
      ->andWhere('om.startDate BETWEEN :from AND :to')
      ->setParameter('organization', $organization)
      ->setParameter('status', MembershipStatus::ACTIVE)
      ->setParameter('from', $from)
      ->setParameter('to', $to)
      ->getQuery()
      ->getSingleScalarResult();
  }

  /**
   * @return array<int, array{label: string, count: int}>
   */
  public function getGenderDistribution(HealthcareOrganization $organization): array
  {
    $rows = $this->createQueryBuilder('om')
      ->select('u.gender AS label, COUNT(om.id) AS count')
      ->innerJoin('om.user', 'u')
      ->andWhere('om.organization = :organization')
      ->andWhere('om.status = :status')
      ->andWhere('om.deletedAt IS NULL')
      ->andWhere('u INSTANCE OF ' . Patient::class)
      ->groupBy('u.gender')
      ->setParameter('organization', $organization)
      ->setParameter('status', MembershipStatus::ACTIVE)
      ->getQuery()
      ->getArrayResult();

    return array_map(
      static fn (array $row) => [
        'label' => $row['label']?->value ?? 'UNSPECIFIED',
        'count' => (int) $row['count'],
      ],
      $rows
    );
  }

  /**
   * @return array<int, array{label: string, count: int}>
   */
  public function getAgeGroupDistribution(HealthcareOrganization $organization): array
  {
    $conn = $this->getEntityManager()->getConnection();
    $sql = <<<SQL
      SELECT
        CASE
          WHEN p.date_of_birth IS NULL THEN 'Non renseigné'
          WHEN TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) < 18 THEN '0-17 ans'
          WHEN TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) BETWEEN 18 AND 34 THEN '18-34 ans'
          WHEN TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) BETWEEN 35 AND 49 THEN '35-49 ans'
          WHEN TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) BETWEEN 50 AND 64 THEN '50-64 ans'
          ELSE '65+ ans'
        END AS label,
        COUNT(*) AS count
      FROM healthcare_organization_memberships om
      INNER JOIN identity_users u ON u.id = om.user_id AND u.user_type = 'patient'
      INNER JOIN identity_patients p ON p.id = u.id
      WHERE om.organization_id = :organizationId
        AND om.status = :status
        AND om.deleted_at IS NULL
      GROUP BY label
      ORDER BY label
    SQL;

    return $conn->executeQuery($sql, [
      'organizationId' => $organization->getId(),
      'status' => MembershipStatus::ACTIVE->value,
    ])->fetchAllAssociative();
  }
}
