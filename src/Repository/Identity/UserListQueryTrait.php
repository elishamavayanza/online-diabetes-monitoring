<?php

namespace App\Repository\Identity;

use App\Entity\Healthcare\MembershipStatus;
use Doctrine\ORM\QueryBuilder;

/**
 * Recherche paginée multi-critères sur les entités héritant de User
 * (User, Patient, HealthcareProfessional).
 *
 * Non utilisé sans paramètres de pagination dans les services : il s'agit
 * d'un chemin de requête ADDITIF (SQL avec COUNT + LIMIT/OFFSET), les
 * appels sans pagination conservent leur comportement historique basé sur
 * findBy().
 *
 * Le chargement s'effectue en deux temps : d'abord les IDs de la page
 * (COUNT + LIMIT/OFFSET), puis les entités complètes avec JOIN FETCH sur
 * organizationMemberships + organization. Cela évite le N+1 lors de la
 * construction des DTO et le risque de MAX_RESULTS incorrect sur une
 * collection (pagination d'une relation to-many).
 */
trait UserListQueryTrait
{
    /**
     * Chemin non paginé : récupère toutes les racines non supprimées avec
     * leurs membership + organisation hydratés en JOIN FETCH (évite le N+1
     * des DTO et des filtres organisation en PHP).
     *
     * @param class-string $rootClass Classe racine (User, Patient ou HealthcareProfessional)
     *
     * @return object[]
     */
    public function findAllWithOrganizationMemberships(string $rootClass): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from($rootClass, 'u')
            ->leftJoin('u.organizationMemberships', 'om')
            ->addSelect('om')
            ->leftJoin('om.organization', 'o')
            ->addSelect('o')
            ->where('u.deletedAt IS NULL')
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Hydrate les membership + organisation des racines dont les ids sont
     * fournis en une seule requête (les entités déjà connues de l'identity map
     * sont complétées sans requête supplémentaire par entité).
     *
     * @param class-string $rootClass Classe racine (User, Patient ou HealthcareProfessional)
     * @param string[]|int[] $ids
     *
     * @return object[]
     */
    public function findWithMembershipsByIds(string $rootClass, array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        return $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from($rootClass, 'u')
            ->leftJoin('u.organizationMemberships', 'om')
            ->addSelect('om')
            ->leftJoin('om.organization', 'o')
            ->addSelect('o')
            ->where('u.deletedAt IS NULL')
            ->andWhere('u.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * Applique les filtres communs (org ROOT, filtre org, recherche, rôles).
     */
    private function applyUserListFilters(
        QueryBuilder $qb,
        ?string $organizationId,
        bool $isSuperAdmin,
        ?string $q,
        ?array $roles,
        ?string $organizationFilter
    ): void {
        if (!$isSuperAdmin && $organizationId !== null) {
            $qb->join('u.organizationMemberships', 'om')
                ->andWhere('om.organization = :org')
                ->andWhere('om.status IN (:statuses)')
                ->setParameter('org', $organizationId)
                ->setParameter('statuses', [MembershipStatus::ACTIVE, MembershipStatus::SUSPENDED]);
        }

        if ($organizationFilter !== null) {
            $qb->join('u.organizationMemberships', 'omf')
                ->andWhere('omf.organization = :orgFilter')
                ->setParameter('orgFilter', $organizationFilter);
        }

        if ($q !== null && trim($q) !== '') {
            $qb->andWhere('(LOWER(u.fullName) LIKE :q OR LOWER(u.email) LIKE :q)')
                ->setParameter('q', '%' . mb_strtolower(trim($q)) . '%');
        }

        if ($roles !== null && $roles !== []) {
            $roleConds = [];
            foreach (array_values($roles) as $i => $role) {
                $roleConds[] = 'u.roles LIKE :role' . $i;
                $qb->setParameter('role' . $i, '%"' . $role . '"%');
            }
            $qb->andWhere('(' . implode(' OR ', $roleConds) . ')');
        }
    }

    /**
     * @param class-string $rootClass Classe racine de la requête
     *                                (User, Patient ou HealthcareProfessional)
     *
     * @return array{items: object[], total: int}
     */
    public function searchPaginated(
        string $rootClass,
        ?string $organizationId,
        bool $isSuperAdmin,
        ?string $q,
        ?string $sort,
        string $order,
        int $page,
        int $limit,
        ?array $roles = null,
        ?string $organizationFilter = null
    ): array {
        $em = $this->getEntityManager();
        $filterQb = $em->createQueryBuilder()
            ->select('u')
            ->from($rootClass, 'u')
            ->where('u.deletedAt IS NULL');

        $this->applyUserListFilters($filterQb, $organizationId, $isSuperAdmin, $q, $roles, $organizationFilter);

        // Liste blanche de tri : empêche toute injection de propriété DQL.
        $whitelist = [
            'createdAt' => 'u.createdAt',
            'fullName'  => 'u.fullName',
            'email'     => 'u.email',
        ];
        $sortField = $whitelist[$sort] ?? 'u.createdAt';
        $direction = strtolower($order) === 'asc' ? 'ASC' : 'DESC';

        $total = (int) (clone $filterQb)
            ->select('COUNT(DISTINCT u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // IDs de la page uniquement (LIMIT/OFFSET sûr, sans collection).
        $ids = (clone $filterQb)
            ->select('u.id')
            ->orderBy($sortField, $direction)
            ->addOrderBy('u.id', $direction)
            ->setFirstResult(max(0, ($page - 1) * $limit))
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getSingleColumnResult();

        if ($ids === []) {
            return ['items' => [], 'total' => $total];
        }

        // Hydratation complète avec JOIN FETCH pour éviter le N+1 des DTO.
        $items = $em->createQueryBuilder()
            ->select('u')
            ->from($rootClass, 'u')
            ->leftJoin('u.organizationMemberships', 'fom')
            ->addSelect('fom')
            ->leftJoin('fom.organization', 'forg')
            ->addSelect('forg')
            ->where('u.deletedAt IS NULL AND u.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy($sortField, $direction)
            ->addOrderBy('u.id', $direction)
            ->getQuery()
            ->getResult();

        return ['items' => $items, 'total' => $total];
    }
}