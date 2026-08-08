<?php

namespace App\Service\Communication;

use App\DTO\Feedback;
use App\DTO\Request\Communication\ConversationParticipantRequestDTO;
use App\Mapper\Communication\ConversationParticipantMapper;
use App\Repository\Communication\ConversationParticipantRepository;
use App\Repository\Communication\ConversationRepository;
use App\Repository\Identity\UserRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ConversationParticipantService
{
    public function __construct(
        private readonly ConversationParticipantRepository $repository,
        private readonly ConversationRepository $conversationRepository,
        private readonly UserRepository $userRepository,
        private readonly ConversationParticipantMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(ConversationParticipantRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::SEND_MESSAGE->value);

            $conversation = $this->conversationRepository->find($dto->conversationId);
            if (!$conversation) {
                return $feedback->setErrorFlushDescription("Conversation introuvable.")->autoInitFlush();
            }

            $user = $this->userRepository->find($dto->userId);
            if (!$user) {
                return $feedback->setErrorFlushDescription("Utilisateur introuvable.")->autoInitFlush();
            }

            $participant = $this->mapper->mapRequestToEntity($dto, $conversation, $user);

            $this->entityManager->persist($participant);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($participant))
                ->setFlushDescription("Participant ajouté à la conversation avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
