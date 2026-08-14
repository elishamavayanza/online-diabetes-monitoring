<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\OrganizationMembershipRequestDTO;
use App\Mapper\Healthcare\OrganizationMembershipMapper;
use App\Repository\Healthcare\DepartmentRepository;
use App\Repository\Healthcare\HealthcareFacilityRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
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
        private readonly HealthcareOrganizationRepository $organizationRepository,
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
            $this->securityService->checkPermission(SecurityAction::MANAGE_ROLES->value);

            $membership = $this->membershipRepository->find($id);
            if (!$membership) {
                return $feedback->setErrorFlushDescription("Adhésion introuvable.")->autoInitFlush();
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

    public function create(OrganizationMembershipRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_ROLES->value);

            $user = $this->userRepository->find($dto->userId);
            if (!$user) {
                return $feedback->setErrorFlushDescription("Utilisateur introuvable.")->autoInitFlush();
            }

            $organization = $this->organizationRepository->find($dto->organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
            }

            $facility = $dto->facilityId ? $this->facilityRepository->find($dto->facilityId) : null;
            $department = $dto->departmentId ? $this->departmentRepository->find($dto->departmentId) : null;

            $membership = $this->mapper->mapRequestToEntity($dto, $user, $organization, $facility, $department);

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
            $this->securityService->checkPermission(SecurityAction::MANAGE_ROLES->value);

            $membership = $this->membershipRepository->find($id);
            if (!$membership) {
                return $feedback->setErrorFlushDescription("Adhésion introuvable.")->autoInitFlush();
            }

            $user = $dto->userId ? $this->userRepository->find($dto->userId) : $membership->getUser();
            if (!$user) {
                return $feedback->setErrorFlushDescription("Utilisateur introuvable.")->autoInitFlush();
            }

            $organization = $dto->organizationId ? $this->organizationRepository->find($dto->organizationId) : $membership->getOrganization();
            if (!$organization) {
                return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
            }

            $facility = $dto->facilityId !== null ? $this->facilityRepository->find($dto->facilityId) : $membership->getFacility();
            $department = $dto->departmentId !== null ? $this->departmentRepository->find($dto->departmentId) : $membership->getDepartment();

            $this->mapper->mapRequestToEntity($dto, $user, $organization, $facility, $department, $membership);

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
            $this->securityService->checkPermission(SecurityAction::MANAGE_ROLES->value);

            $membership = $this->membershipRepository->find($id);
            if (!$membership) {
                return $feedback->setErrorFlushDescription("Adhésion introuvable.")->autoInitFlush();
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
