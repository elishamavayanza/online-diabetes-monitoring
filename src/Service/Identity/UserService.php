<?php

namespace App\Service\Identity;

use App\DTO\Feedback;
use App\DTO\Request\Identity\UserCreateRequestDTO;
use App\Entity\Common\Gender;
use App\Entity\Common\UserStatus;
use App\Entity\Healthcare\OrganizationMembership;
use App\Entity\Identity\Patient;
use App\Entity\Identity\Role;
use App\Mapper\Identity\UserMapper;
use App\Repository\Identity\UserRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserService
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * Liste tous les utilisateurs de l'organisation de l'administrateur connecté.
     *
     * Contrat additif : si $page est fourni, la réponse devient
     * paginée { items, total, page, limit, totalPages } et la recherche/tri
     * sont exécutés côté serveur. Sans $page, le comportement historique
     * (tableau complet) est conservé.
     */
    public function getAll(
        ?int $page = null,
        ?int $limit = 20,
        ?string $q = null,
        ?string $sort = null,
        string $order = 'desc',
        ?string $role = null,
        ?string $organization = null
    ): Feedback
    {
        $feedback = new Feedback();

        try {
            $currentUser = $this->securityService->getCurrentUser();
            $targetOrganization = null;
            $isSuperAdmin = $this->securityService->isSuperAdmin();

            if (!$isSuperAdmin) {
                foreach ($currentUser->getOrganizationMemberships() as $membership) {
                    if ($membership->getStatus()->isActive() && $membership->getOrganization() !== null) {
                        $targetOrganization = $membership->getOrganization();
                        break;
                    }
                }

                if (!$targetOrganization) {
                    throw new AccessDeniedException('Aucune organisation active trouvée pour cet administrateur.');
                }

                $this->securityService->checkOrganizationAccess(
                    $targetOrganization,
                    SecurityAction::VIEW
                );
            }

            // Chemin paginé : requête SQL ciblée (pas de chargement complet).
            if ($page !== null) {
                $roles = match ($role) {
                    'admin' => [Role::ROLE_ADMIN->value, Role::ROLE_ROOT->value],
                    'professional' => [Role::ROLE_CLINICIAN->value, Role::ROLE_NUTRITIONIST->value],
                    'patient' => [Role::ROLE_PATIENT->value],
                    default => null,
                };

                $result = $this->repository->searchPaginated(
                    \App\Entity\Identity\User::class,
                    $targetOrganization?->getId(),
                    $isSuperAdmin,
                    $q,
                    $sort,
                    $order,
                    $page,
                    $limit ?? 20,
                    $roles,
                    $organization
                );

                $items = array_map(
                    fn ($user) => $this->mapper->mapEntityToResponse($user),
                    $result['items']
                );

                return $feedback
                    ->setData([
                        'items'      => array_values($items),
                        'total'      => $result['total'],
                        'page'       => $page,
                        'limit'      => $limit ?? 20,
                        'totalPages' => (int) ceil($result['total'] / max(1, $limit ?? 20)),
                    ])
                    ->setFlushDescription('Liste des utilisateurs récupérée avec succès.')
                    ->autoInitFlush();
            }

            $users = $this->repository->findAllWithOrganizationMemberships(User::class);

            // Le Super Admin voit tous les utilisateurs de la plateforme ; sinon
            // on filtre par l'organisation de l'administrateur connecté.
            if (!$isSuperAdmin) {
                $users = array_filter($users, function ($user) use ($targetOrganization) {
                    foreach ($user->getOrganizationMemberships() as $membership) {
                        if (
                            $membership->getStatus()->isActive() &&
                            $membership->getOrganization() !== null &&
                            $membership->getOrganization()->getId() === $targetOrganization->getId()
                        ) {
                            return true;
                        }
                    }
                    return false;
                });
            }

            $data = array_map(
                fn ($user) => $this->mapper->mapEntityToResponse($user),
                $users
            );

            return $feedback
                ->setData(array_values($data))
                ->setFlushDescription('Liste des utilisateurs de l’organisation récupérée avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur lors de la récupération des utilisateurs : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    /**
     * Met à jour un compte utilisateur.
     */
    public function update(string $id, UserCreateRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $currentUser = $this->securityService->getCurrentUser();
            $targetOrganization = null;

            foreach ($currentUser->getOrganizationMemberships() as $membership) {
                if ($membership->getStatus()->isActive() && $membership->getOrganization() !== null) {
                    $targetOrganization = $membership->getOrganization();
                    break;
                }
            }

            if (!$targetOrganization) {
                throw new AccessDeniedException('Aucune organisation active trouvée.');
            }

            $this->securityService->checkOrganizationAccess(
                $targetOrganization,
                SecurityAction::MANAGE_USERS
            );

            $user = $this->repository->find($id);
            if (!$user) {
                return $feedback->setErrorFlushDescription('Utilisateur introuvable.')->autoInitFlush();
            }

            if ($dto->email !== null) {
                $existingUser = $this->repository->findOneBy(['email' => $dto->email]);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    return $feedback->setErrorFlushDescription('Cet e-mail est déjà utilisé.')->autoInitFlush();
                }
                $user->setEmail($dto->email);
            }

            if ($dto->fullName !== null) {
                $user->setFullName($dto->fullName);
            }

            if ($dto->phone !== null) {
                $user->setPhone($dto->phone);
            }

            if ($dto->gender !== null) {
                $user->setGender(Gender::from($dto->gender));
            }

            if (!empty($dto->password)) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
                $user->setPasswordHash($hashedPassword);
            }

            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($user))
                ->setFlushDescription('Utilisateur mis à jour avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur lors de la mise à jour : ' . $e->getMessage())->autoInitFlush();
        }
    }

    /**
     * Crée un compte utilisateur (Patient).
     */
    public function create(UserCreateRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $currentUser = $this->securityService->getCurrentUser();
            $targetOrganization = null;

            foreach ($currentUser->getOrganizationMemberships() as $membership) {
                if ($membership->getStatus()->isActive() && $membership->getOrganization() !== null) {
                    $targetOrganization = $membership->getOrganization();
                    break;
                }
            }

            if (!$targetOrganization) {
                throw new AccessDeniedException('Aucune organisation active trouvée pour cet administrateur.');
            }

            $this->securityService->checkOrganizationAccess(
                $targetOrganization,
                SecurityAction::MANAGE_USERS
            );

            if ($this->repository->findOneBy(['email' => $dto->email])) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Un utilisateur avec cet e-mail existe déjà.'
                    )
                    ->autoInitFlush();
            }

            $user = new Patient();

            $user
                ->setEmail($dto->email)
                ->setFullName($dto->fullName)
                ->setPhone($dto->phone ?? null)
                ->setGender($dto->gender !== null ? Gender::from($dto->gender) : null)
                ->setLocale($dto->locale ?? 'fr');

            $user->setPasswordHash(
                $this->passwordHasher->hashPassword(
                    $user,
                    $dto->password
                )
            );

            $user->setStatus(
                UserStatus::PENDING_ACTIVATION
            );

            $user->setRoles([
                Role::ROLE_PATIENT->value
            ]);

            $this->entityManager->persist($user);

            $orgMembership = new OrganizationMembership();
            $orgMembership->setUser($user);
            $orgMembership->setOrganization($targetOrganization);
            $orgMembership->setStartDate(new \DateTimeImmutable());
            $orgMembership->setStatus(\App\Entity\Healthcare\MembershipStatus::ACTIVE);

            $user->getOrganizationMemberships()->add($orgMembership);

            $this->entityManager->persist($orgMembership);
            $this->entityManager->flush();

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($user)
                )
                ->setFlushDescription(
                    'Compte utilisateur créé et rattaché à l’organisation avec succès.'
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Erreur lors de la création : ' . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }
}
