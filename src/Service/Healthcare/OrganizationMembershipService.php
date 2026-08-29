<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\OrganizationMembershipRequestDTO;
use App\Mapper\Healthcare\OrganizationMembershipMapper;
use App\Repository\Healthcare\DepartmentRepository;
use App\Repository\Healthcare\HealthcareFacilityRepository;
use App\Repository\Healthcare\OrganizationMembershipRepository;
use App\Repository\Identity\UserRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OrganizationMembershipService
{
    public function __construct(
        private readonly OrganizationMembershipRepository $membershipRepository,
        private readonly UserRepository $userRepository,
        private readonly HealthcareFacilityRepository $facilityRepository,
        private readonly DepartmentRepository $departmentRepository,
        private readonly OrganizationMembershipMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function getById(string $id): Feedback
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

            $membership = $this->membershipRepository->find($id);
            if (!$membership) {
                return $feedback->setErrorFlushDescription("Adhésion introuvable.")->autoInitFlush();
            }

            if ($membership->getOrganization() !== $targetOrganization) {
                throw new AccessDeniedException("Vous ne pouvez accéder qu'aux adhésions de votre propre organisation.");
            }

            $feedback->setData($this->mapper->mapEntityToResponse($membership))
                ->setFlushDescription("Adhésion récupérée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    /**
     * Récupère toutes les personnes (utilisateurs rattachés via adhésion) d'une organisation spécifique.
     */
    public function getAllUsersForOrganization(\App\Entity\Healthcare\HealthcareOrganization $targetOrganization): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkOrganizationAccess(
                $targetOrganization,
                SecurityAction::MANAGE_USERS
            );

            $memberships = $this->membershipRepository->findBy(['organization' => $targetOrganization]);

            $data = array_map(
                fn($membership) => $this->mapper->mapEntityToResponse($membership),
                $memberships
            );

            $feedback->setData($data)
                ->setFlushDescription("Liste de toutes les personnes de l'organisation récupérée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    /**
     * Récupère tous les patients d'une organisation spécifique.
     */
    public function getAllPatientsForOrganization(\App\Entity\Healthcare\HealthcareOrganization $targetOrganization): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkOrganizationAccess(
                $targetOrganization,
                SecurityAction::MANAGE_USERS
            );

            $memberships = $this->membershipRepository->findBy(['organization' => $targetOrganization]);

            // Filtrer uniquement les membres dont l'utilisateur associé est un Patient
            $patientMemberships = array_filter($memberships, function($membership) {
                $user = $membership->getUser();
                return $user instanceof \App\Entity\Identity\Patient || in_array(\App\Entity\Identity\Role::ROLE_PATIENT->value, $user->getRoles(), true);
            });

            $data = array_map(
                fn($membership) => $this->mapper->mapEntityToResponse($membership),
                $patientMemberships
            );

            // Réindexer le tableau pour éviter des indices associatifs non séquentiels en JSON
            $feedback->setData(array_values($data))
                ->setFlushDescription("Liste de tous les patients de l'organisation récupérée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function create(OrganizationMembershipRequestDTO $dto): Feedback
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

            $user = $this->userRepository->find($dto->userId);
            if (!$user) {
                return $feedback->setErrorFlushDescription("Utilisateur introuvable.")->autoInitFlush();
            }

            $facility = $dto->facilityId ? $this->facilityRepository->find($dto->facilityId) : null;
            $department = $dto->departmentId ? $this->departmentRepository->find($dto->departmentId) : null;

            // === VÉRIFICATION D'UNICITÉ ===
            // Empêche la création d'un doublon pour le même utilisateur dans la même organisation
            $criteria = [
                'user' => $user,
                'organization' => $targetOrganization
            ];

            if ($facility) {
                $criteria['facility'] = $facility;
            }
            if ($department) {
                $criteria['department'] = $department;
            }

            $existingMembership = $this->membershipRepository->findOneBy($criteria);
            if ($existingMembership) {
                return $feedback->setErrorFlushDescription("Cet utilisateur possède déjà une adhésion active pour cette structure.")->autoInitFlush();
            }
            // =============================

            $membership = $this->mapper->mapRequestToEntity($dto, $user, $targetOrganization, $facility, $department);

            $this->entityManager->persist($membership);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($membership))
                ->setFlushDescription("Appartenance à l'organisation enregistrée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $id, OrganizationMembershipRequestDTO $dto): Feedback
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

            $membership = $this->membershipRepository->find($id);
            if (!$membership) {
                return $feedback->setErrorFlushDescription("Adhésion introuvable.")->autoInitFlush();
            }

            if ($membership->getOrganization() !== $targetOrganization) {
                throw new AccessDeniedException("Vous ne pouvez modifier que les adhésions de votre propre organisation.");
            }

            $user = $dto->userId ? $this->userRepository->find($dto->userId) : $membership->getUser();
            if (!$user) {
                return $feedback->setErrorFlushDescription("Utilisateur introuvable.")->autoInitFlush();
            }

            $facility = $dto->facilityId !== null ? $this->facilityRepository->find($dto->facilityId) : $membership->getFacility();
            $department = $dto->departmentId !== null ? $this->departmentRepository->find($dto->departmentId) : $membership->getDepartment();

            $this->mapper->mapRequestToEntity($dto, $user, $targetOrganization, $facility, $department, $membership);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($membership))
                ->setFlushDescription("Adhésion mise à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(string $id): Feedback
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

            $membership = $this->membershipRepository->find($id);
            if (!$membership) {
                return $feedback->setErrorFlushDescription("Adhésion introuvable.")->autoInitFlush();
            }

            if ($membership->getOrganization() !== $targetOrganization) {
                throw new AccessDeniedException("Vous ne pouvez supprimer que les adhésions de votre propre organisation.");
            }

            $this->entityManager->remove($membership);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Adhésion supprimée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
