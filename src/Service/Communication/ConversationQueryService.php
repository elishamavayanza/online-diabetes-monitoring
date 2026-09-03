<?php

namespace App\Service\Communication;

use App\DTO\Feedback;
use App\DTO\Response\Communication\ConversationSummaryResponseDTO;
use App\DTO\Response\Communication\MessageAttachmentResponseDTO;
use App\DTO\Response\Communication\MessageDetailResponseDTO;
use App\Entity\Communication\Conversation;
use App\Entity\Communication\Message;
use App\Entity\Healthcare\CareTeamAssignment;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use App\Repository\Communication\ConversationRepository;
use App\Repository\Communication\MessageReadReceiptRepository;
use App\Repository\Communication\MessageRepository;
use App\Repository\Healthcare\CareTeamAssignmentRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ConversationQueryService
{
    public function __construct(
        private readonly ConversationRepository $conversationRepository,
        private readonly MessageRepository $messageRepository,
        private readonly MessageReadReceiptRepository $readReceiptRepository,
        private readonly PatientRepository $patientRepository,
        private readonly CareTeamAssignmentRepository $careTeamAssignmentRepository,
        private readonly SecurityServiceInterface $securityService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function getMine(): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::READ_MESSAGE->value);
            $currentUser = $this->securityService->getCurrentUser();

            // Un patient consulte ses propres échanges. Les professionnels, eux,
            // ne voient que ceux des patients qui leur sont attribués.
            if ($currentUser instanceof Patient) {
                $conversations = $this->conversationRepository->findByPatientUser((string) $currentUser->getId());
            } elseif ($currentUser instanceof HealthcareProfessional) {
                $assignments = $this->careTeamAssignmentRepository->findActiveByProfessional($currentUser);
                $patientIds = array_map(
                    static fn (CareTeamAssignment $assignment) => (string) $assignment->getPatient()?->getId(),
                    $assignments
                );
                $conversations = $this->conversationRepository->findByPatientUserIds($patientIds);
            } else {
                $conversations = [];
            }
            $summaries = array_map(
                fn (Conversation $c) => $this->buildSummary($c, $currentUser),
                $conversations
            );

            $feedback->setData($summaries)
                ->setFlushDescription('Conversations récupérées avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function getByPatient(string $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::READ_MESSAGE->value);
            $currentUser = $this->securityService->getCurrentUser();

            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription('Patient introuvable.')->autoInitFlush();
            }

            $conversations = $this->conversationRepository->findByPatientUser((string) $patient->getId());
            $summaries = array_map(
                fn (Conversation $c) => $this->buildSummary($c, $currentUser),
                $conversations
            );

            $feedback->setData($summaries)
                ->setFlushDescription('Conversations récupérées avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function getMessages(string $conversationId): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::READ_MESSAGE->value);
            $currentUser = $this->securityService->getCurrentUser();

            $conversation = $this->conversationRepository->find($conversationId);
            if (!$conversation) {
                return $feedback->setErrorFlushDescription('Conversation introuvable.')->autoInitFlush();
            }

            $patient = $conversation->getPatient();
            if (!$patient instanceof Patient) {
                return $feedback->setErrorFlushDescription('Patient de la conversation introuvable.')->autoInitFlush();
            }
            $this->securityService->checkPatientAccess($patient, SecurityAction::READ_MESSAGE);

            $messages = $this->messageRepository->findByConversationOrderedAsc($conversation);
            $details = array_map(
                fn (Message $message) => $this->buildMessageDetail($message, $currentUser),
                $messages
            );

            $this->markConversationAsRead($conversation, $currentUser);

            $feedback->setData($details)
                ->setFlushDescription('Messages récupérés avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    private function buildSummary(Conversation $conversation, User $currentUser): ConversationSummaryResponseDTO
    {
        $messages = $conversation->getMessages()->toArray();
        usort($messages, static fn (Message $a, Message $b) => $b->getSentAt() <=> $a->getSentAt());
        $lastMessage = $messages[0] ?? null;

        $patient = $conversation->getPatient();
        $patientName = $patient instanceof Patient ? $patient->getFullName() : null;

        return new ConversationSummaryResponseDTO(
            id: (string) $conversation->getId(),
            subject: $conversation->getSubject() ?? 'Conversation',
            patientId: (string) $patient?->getId(),
            patientName: $patientName,
            lastMessageContent: $lastMessage?->getContent(),
            lastMessageAt: $lastMessage?->getSentAt(),
            unreadCount: $this->messageRepository->countUnreadForUser($conversation, $currentUser),
            createdAt: $conversation->getCreatedAt(),
        );
    }

    private function buildMessageDetail(Message $message, User $currentUser): MessageDetailResponseDTO
    {
        $senderId = (string) $message->getSender()?->getId();
        $currentUserId = (string) $currentUser->getId();
        $isMine = $senderId === $currentUserId;

        // Récupérer le nom de l'expéditeur
        $sender = $message->getSender();
        $authorName = $sender && method_exists($sender, 'getFullName')
            ? $sender->getFullName()
            : null;

        $attachments = array_map(
            static fn ($attachment) => MessageAttachmentResponseDTO::fromEntity(
                $attachment,
                sprintf('/api/message-attachments/%s/download', $attachment->getId())
            ),
            $message->getAttachments()->toArray()
        );

        if ($isMine) {
            $receipts = $this->readReceiptRepository->findReadReceiptsForMessage($message);
            $recipientRead = null;
            foreach ($receipts as $receipt) {
                if ((string) $receipt->getUser()?->getId() !== $currentUserId) {
                    $recipientRead = $receipt->getReadAt();
                    break;
                }
            }

            return MessageDetailResponseDTO::fromEntity(
                $message,
                true,
                $recipientRead !== null,
                $recipientRead,
                $attachments,
                $authorName
            );
        }

        $myReceipt = $this->readReceiptRepository->findByMessageAndUser($message, $currentUser);

        return MessageDetailResponseDTO::fromEntity(
            $message,
            false,
            $myReceipt !== null,
            $myReceipt?->getReadAt(),
            $attachments,
            $authorName
        );
    }

    private function markConversationAsRead(Conversation $conversation, User $currentUser): void
    {
        $unreadMessages = $this->messageRepository->findUnreadIncomingForUser($conversation, $currentUser);

        foreach ($unreadMessages as $message) {
            $receipt = new \App\Entity\Communication\MessageReadReceipt();
            $receipt->setMessage($message);
            $receipt->setUser($currentUser);
            $receipt->setReadAt(new \DateTimeImmutable());
            $this->entityManager->persist($receipt);
        }

        if ($unreadMessages !== []) {
            $this->entityManager->flush();
        }
    }
}
