<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\HealthcareOrganizationRequestDTO;
use App\Mapper\Healthcare\HealthcareOrganizationMapper;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
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

    public function update(string $id, HealthcareOrganizationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_ORGANIZATION->value);

            $organization = $this->repository->find($id);
            if (!$organization) {
                $feedback->setErrorFlushDescription("Organisation de santé introuvable.")->autoInitFlush();
                return $feedback;
            }

            // Utilisation du mapper pour mettre à jour l'entité existante
            $this->mapper->mapRequestToEntity($dto, $organization);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($organization))
                ->setFlushDescription("Organisation de santé mise à jour avec succès.")
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
            $this->securityService->checkPermission(SecurityAction::MANAGE_ORGANIZATION->value);

            $organization = $this->repository->find($id);
            if (!$organization) {
                $feedback->setErrorFlushDescription("Organisation de santé introuvable.")->autoInitFlush();
                return $feedback;
            }

            $this->entityManager->remove($organization);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Organisation de santé supprimée avec succès.")->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function suspend(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_ORGANIZATION->value);

            $organization = $this->repository->find($id);
            if (!$organization) {
                $feedback->setErrorFlushDescription("Organisation de santé introuvable.")->autoInitFlush();
                return $feedback;
            }

            // Supposons que votre entité possède un booléen ou un statut "active"
            $organization->setActive(false);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($organization))
                ->setFlushDescription("Organisation de santé suspendue avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
