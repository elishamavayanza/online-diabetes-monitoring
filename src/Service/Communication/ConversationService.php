<?php

namespace App\Service\Communication;

use App\DTO\Feedback;
use App\DTO\Request\Communication\ConversationRequestDTO;
use App\Mapper\Communication\ConversationMapper;
use App\Repository\Communication\ConversationRepository;
use App\Repository\Identity\PatientRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ConversationService
{
    public function __construct(
        private readonly ConversationRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly ConversationMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(ConversationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            // 1. Récupération de l'utilisateur connecté (le créateur)
            $createdBy = $this->securityService->getCurrentUser();

            // 2. Gestion de l'organisation et vérification des accès
            $organization = null;
            if ($dto->organizationId) {
                $organization = $this->organizationRepository->find($dto->organizationId);
                if (!$organization) {
                    return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
                }
                $this->securityService->checkOrganizationAccess($organization, SecurityAction::CREATE_CONVERSATION);
            } else {
                $this->securityService->checkPermission(SecurityAction::CREATE_CONVERSATION->value);
            }

            // 3. Récupération du patient concerné
            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            // 4. Mappage et persistance
            $conversation = $this->mapper->mapRequestToEntity($dto, $createdBy, $patient, $organization);

            $this->entityManager->persist($conversation);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($conversation))
                ->setFlushDescription("Conversation créée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function get(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $conversation = $this->repository->find($id);
            if (!$conversation) {
                return $feedback->setErrorFlushDescription("Conversation introuvable.")->autoInitFlush();
            }

            // Vérification des accès si liée à une organisation
            if ($conversation->getOrganization()) {
                $this->securityService->checkOrganizationAccess($conversation->getOrganization(), SecurityAction::READ_MESSAGE);
            } else {
                $this->securityService->checkPermission(SecurityAction::READ_MESSAGE->value);
            }

            $feedback->setData($this->mapper->mapEntityToResponse($conversation))
                ->setFlushDescription("Conversation récupérée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $id, ConversationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $conversation = $this->repository->find($id);
            if (!$conversation) {
                return $feedback->setErrorFlushDescription("Conversation introuvable.")->autoInitFlush();
            }

            // Vérification des accès sur l'organisation actuelle ou la nouvelle
            $organization = null;
            if ($dto->organizationId) {
                $organization = $this->organizationRepository->find($dto->organizationId);
                if (!$organization) {
                    return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
                }
                $this->securityService->checkOrganizationAccess($organization, SecurityAction::CREATE_CONVERSATION);
            } else {
                $this->securityService->checkPermission(SecurityAction::CREATE_CONVERSATION->value);
            }

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            // Mise à jour via le mapper en passant l'entité existante
            $currentUser = $this->securityService->getCurrentUser();
            $this->mapper->mapRequestToEntity($dto, $currentUser, $patient, $organization, $conversation);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($conversation))
                ->setFlushDescription("Conversation mise à jour avec succès.")
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
            $conversation = $this->repository->find($id);
            if (!$conversation) {
                return $feedback->setErrorFlushDescription("Conversation introuvable.")->autoInitFlush();
            }

            // Vérification des accès avant suppression
            if ($conversation->getOrganization()) {
                $this->securityService->checkOrganizationAccess($conversation->getOrganization(), SecurityAction::CREATE_CONVERSATION);
            } else {
                $this->securityService->checkPermission(SecurityAction::CREATE_CONVERSATION->value);
            }

            $this->entityManager->remove($conversation);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Conversation supprimée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
