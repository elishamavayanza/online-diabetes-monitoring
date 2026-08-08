<?php

namespace App\Service\Communication;

use App\DTO\Feedback;
use App\DTO\Request\Communication\MessageAttachmentRequestDTO;
use App\Mapper\Communication\MessageAttachmentMapper;
use App\Repository\Communication\MessageAttachmentRepository;
use App\Repository\Communication\MessageRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MessageAttachmentService
{
    public function __construct(
        private readonly MessageAttachmentRepository $repository,
        private readonly MessageRepository $messageRepository,
        private readonly MessageAttachmentMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(MessageAttachmentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::SEND_MESSAGE->value);

            $message = $this->messageRepository->find($dto->messageId);
            if (!$message) {
                return $feedback->setErrorFlushDescription("Message introuvable.")->autoInitFlush();
            }

            $attachment = $this->mapper->mapRequestToEntity($dto, $message);

            $this->entityManager->persist($attachment);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($attachment))
                ->setFlushDescription("Pièce jointe ajoutée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
