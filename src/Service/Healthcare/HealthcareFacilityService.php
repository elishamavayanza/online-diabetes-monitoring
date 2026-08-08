<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\HealthcareFacilityRequestDTO;
use App\Mapper\Healthcare\HealthcareFacilityMapper;
use App\Repository\Healthcare\HealthcareFacilityRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HealthcareFacilityService
{
    public function __construct(
        private readonly HealthcareFacilityRepository $facilityRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly HealthcareFacilityMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(HealthcareFacilityRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_FACILITY->value);

            $organization = $this->organizationRepository->find($dto->organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($organization, SecurityAction::MANAGE_FACILITY);

            $facility = $this->mapper->mapRequestToEntity($dto, $organization);

            $this->entityManager->persist($facility);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($facility))
                ->setFlushDescription("Établissement de santé créé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
