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
     */
    public function getAll(): Feedback
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
                SecurityAction::VIEW
            );

            $users = $this->repository->findBy(
                ['deletedAt' => null],
                ['createdAt' => 'DESC']
            );

            // Filtrer par l'organisation de l'administrateur connecté
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
