<?php

namespace App\Service\Notification;

use App\DTO\Feedback;
use App\DTO\Request\Notification\NotificationRequestDTO;
use App\Mapper\Notification\NotificationMapper;
use App\Repository\Notification\NotificationRepository;
use App\Repository\Identity\UserRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepository $repository,
        private readonly UserRepository $userRepository,
        private readonly NotificationMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(NotificationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::CREATE_NOTIFICATION->value);

            $user = $this->userRepository->find($dto->userId);
            if (!$user) {
                return $feedback->setErrorFlushDescription("Utilisateur introuvable.")->autoInitFlush();
            }

            $notification = $this->mapper->mapRequestToEntity($dto, $user);

            $this->entityManager->persist($notification);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($notification))
                ->setFlushDescription("Notification créée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
