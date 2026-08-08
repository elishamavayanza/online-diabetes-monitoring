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
}
