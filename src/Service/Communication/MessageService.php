<?php

namespace App\Service\Communication;

use App\DTO\Feedback;
use App\DTO\Request\Communication\MessageRequestDTO;
use App\Mapper\Communication\MessageMapper;
use App\Repository\Communication\MessageRepository;
use App\Repository\Communication\ConversationRepository;
use App\Repository\Identity\UserRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MessageService
{
    public function __construct(
        private readonly MessageRepository $repository,
        private readonly ConversationRepository $conversationRepository,
        private readonly UserRepository $userRepository,
        private readonly MessageMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(MessageRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::SEND_MESSAGE->value);

            $conversation = $this->conversationRepository->find($dto->conversationId);
            if (!$conversation) {
                return $feedback->setErrorFlushDescription("Conversation introuvable.")->autoInitFlush();
            }

            $sender = $this->userRepository->find($dto->senderId);
            if (!$sender) {
                return $feedback->setErrorFlushDescription("Expéditeur introuvable.")->autoInitFlush();
            }

            $message = $this->mapper->mapRequestToEntity($dto, $conversation, $sender);

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($message))
                ->setFlushDescription("Message envoyé avec succès.")
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
            $this->securityService->checkPermission(SecurityAction::READ_MESSAGE->value);

            $message = $this->repository->find($id);
            if (!$message) {
                return $feedback->setErrorFlushDescription("Message introuvable.")->autoInitFlush();
            }

            $feedback->setData($this->mapper->mapEntityToResponse($message))
                ->setFlushDescription("Message récupéré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $id, MessageRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::SEND_MESSAGE->value);

            $message = $this->repository->find($id);
            if (!$message) {
                return $feedback->setErrorFlushDescription("Message introuvable.")->autoInitFlush();
            }

            $conversation = $this->conversationRepository->find($dto->conversationId);
            if (!$conversation) {
                return $feedback->setErrorFlushDescription("Conversation introuvable.")->autoInitFlush();
            }

            $sender = $this->userRepository->find($dto->senderId);
            if (!$sender) {
                return $feedback->setErrorFlushDescription("Expéditeur introuvable.")->autoInitFlush();
            }

            // Mise à jour de l'entité existante via le mapper
            $this->mapper->mapRequestToEntity($dto, $conversation, $sender, $message);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($message))
                ->setFlushDescription("Message mis à jour avec succès.")
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
            $this->securityService->checkPermission(SecurityAction::SEND_MESSAGE->value);

            $message = $this->repository->find($id);
            if (!$message) {
                return $feedback->setErrorFlushDescription("Message introuvable.")->autoInitFlush();
            }

            $this->entityManager->remove($message);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Message supprimé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
