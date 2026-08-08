<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\HealthcareOrganizationRequestDTO;
use App\Mapper\Healthcare\HealthcareOrganizationMapper;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HealthcareOrganizationService
{
    public function __construct(
        private readonly HealthcareOrganizationRepository $repository,
        private readonly HealthcareOrganizationMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(HealthcareOrganizationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_ORGANIZATION->value);

            $organization = $this->mapper->mapRequestToEntity($dto);

            $this->entityManager->persist($organization);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($organization))
                ->setFlushDescription("Organisation de santé créée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
