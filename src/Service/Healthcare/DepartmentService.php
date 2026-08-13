<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\DepartmentRequestDTO;
use App\Mapper\Healthcare\DepartmentMapper;
use App\Repository\Healthcare\DepartmentRepository;
use App\Repository\Healthcare\HealthcareFacilityRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DepartmentService
{
    public function __construct(
        private readonly DepartmentRepository $departmentRepository,
        private readonly HealthcareFacilityRepository $facilityRepository,
        private readonly DepartmentMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(DepartmentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_DEPARTMENT->value);

            $facility = $this->facilityRepository->find($dto->facilityId);
            if (!$facility) {
                return $feedback->setErrorFlushDescription("Établissement introuvable.")->autoInitFlush();
            }

            $department = $this->mapper->mapRequestToEntity($dto, $facility);

            $this->entityManager->persist($department);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($department))
                ->setFlushDescription("Département créé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $id, DepartmentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_DEPARTMENT->value);

            $department = $this->departmentRepository->find($id);
            if (!$department) {
                return $feedback->setErrorFlushDescription("Département introuvable.")->autoInitFlush();
            }

            $facility = $this->facilityRepository->find($dto->facilityId);
            if (!$facility) {
                return $feedback->setErrorFlushDescription("Établissement introuvable.")->autoInitFlush();
            }

            $department->setName($dto->name);
            $department->setSpecialty($dto->specialty);
            $department->setFacility($facility);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($department))
                ->setFlushDescription("Département mis à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function getByFacility(string $facilityId): Feedback
    {
        $feedback = new Feedback();

        try {
            $facility = $this->facilityRepository->find($facilityId);
            if (!$facility) {
                return $feedback->setErrorFlushDescription("Établissement introuvable.")->autoInitFlush();
            }

            $departments = $this->departmentRepository->findBy(['facility' => $facility]);

            $responseDTOs = array_map(
                fn($department) => $this->mapper->mapEntityToResponse($department),
                $departments
            );

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Liste des départements récupérée avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function toggleSuspend(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_DEPARTMENT->value);

            $department = $this->departmentRepository->find($id);
            if (!$department) {
                return $feedback->setErrorFlushDescription("Département introuvable.")->autoInitFlush();
            }

            // Bascule l'état actif/suspendu (assurez-vous que l'entité Department possède un champ active)
            $newStatus = !$department->isActive();
            $department->setActive($newStatus);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($department))
                ->setFlushDescription($newStatus ? "Département réactivé avec succès." : "Département suspendu avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
