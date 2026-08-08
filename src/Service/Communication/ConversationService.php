<?php

namespace App\Service\Communication;

use App\DTO\Feedback;
use App\DTO\Request\Communication\ConversationRequestDTO;
use App\Mapper\Communication\ConversationMapper;
use App\Repository\Communication\ConversationRepository;
use App\Repository\Identity\UserRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ConversationService
{
    public function __construct(
        private readonly ConversationRepository $repository,
        private readonly UserRepository $userRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly ConversationMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(ConversationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::SEND_MESSAGE->value);

            $createdBy = $this->userRepository->find($dto->createdById);
            if (!$createdBy) {
                return $feedback->setErrorFlushDescription("Utilisateur créateur introuvable.")->autoInitFlush();
            }

            $organization = null;
            if ($dto->organizationId) {
                $organization = $this->organizationRepository->find($dto->organizationId);
                if (!$organization) {
                    return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
                }
                $this->securityService->checkOrganizationAccess($organization, SecurityAction::SEND_MESSAGE);
            }

            $conversation = $this->mapper->mapRequestToEntity($dto, $createdBy, $organization);

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
}
