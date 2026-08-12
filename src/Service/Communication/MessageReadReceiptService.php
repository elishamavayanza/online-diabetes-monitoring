<?php

namespace App\Service\Communication;

use App\DTO\Feedback;
use App\DTO\Request\Communication\MessageReadReceiptRequestDTO;
use App\Mapper\Communication\MessageReadReceiptMapper;
use App\Repository\Communication\MessageReadReceiptRepository;
use App\Repository\Communication\MessageRepository;
use App\Repository\Communication\ConversationParticipantRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MessageReadReceiptService
{
    public function __construct(
        private readonly MessageReadReceiptRepository $repository,
        private readonly MessageRepository $messageRepository,
        private readonly MessageReadReceiptMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(MessageReadReceiptRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::SEND_MESSAGE->value);

            $message = $this->messageRepository->find($dto->messageId);
            if (!$message) {
                return $feedback->setErrorFlushDescription("Message introuvable.")->autoInitFlush();
            }

            $participant = $this->participantRepository->find($dto->participantId);
            if (!$participant) {
                return $feedback->setErrorFlushDescription("Participant introuvable.")->autoInitFlush();
            }

            $receipt = $this->mapper->mapRequestToEntity($dto, $message, $participant);

            $this->entityManager->persist($receipt);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($receipt))
                ->setFlushDescription("Accusé de lecture enregistré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
