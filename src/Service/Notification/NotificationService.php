<?php

namespace App\Service\Notification;

use App\DTO\Feedback;
use App\DTO\Request\Notification\NotificationRequestDTO;
use App\Entity\Appointment\ReminderChannel;
use App\Entity\Identity\Role;
use App\Entity\Identity\User;
use App\Entity\Notification\Notification;
use App\Entity\Notification\NotificationType;
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
        private readonly SecurityServiceInterface $securityService,
        private readonly NotificationMailer $notificationMailer
    ) {}

    /**
     * Récupère les notifications filtrées par type
     */
    public function getByType(string $type): Feedback
    {
        $feedback = new Feedback();

        try {
            $notifications = $this->repository->findBy(['type' => $type]);
            $responseDTOs = array_map(fn($notification) => $this->mapper->mapEntityToResponse($notification), $notifications);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Notifications récupérées avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    /**
     * Récupère toutes les notifications visibles pour un utilisateur donné
     */
    public function getForUser(User $currentUser): Feedback
    {
        $feedback = new Feedback();

        try {
            // L'utilisateur connecté ne voit que les notifications qui lui sont assignées directement
            // (que ce soit une notif individuelle, ou les copies créées pour lui lors d'un envoi ORGANIZATION/GLOBAL)
            $notifications = $this->repository->findBy(['user' => $currentUser]);

            $responseDTOs = array_map(fn($notification) => $this->mapper->mapEntityToResponse($notification), $notifications);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Notifications récupérées avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function create(NotificationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::CREATE_NOTIFICATION->value);

            $usersToNotify = [];
            $createdNotifications = [];

            switch (strtoupper($dto->scope)) {
                case 'USER':
                    if (!$dto->userId) {
                        return $feedback->setErrorFlushDescription("L'ID utilisateur est requis pour le scope USER.")->autoInitFlush();
                    }
                    $user = $this->userRepository->find($dto->userId);
                    if ($user) {
                        $usersToNotify[] = $user;
                    }
                    break;

                case 'ORGANIZATION':
                    if (!$dto->organizationId) {
                        return $feedback->setErrorFlushDescription("L'ID de l'organisation est requis pour le scope ORGANIZATION.")->autoInitFlush();
                    }

                    $queryBuilder = $this->userRepository->createQueryBuilder('u')
                        ->join('u.organizationMemberships', 'om')
                        ->join('om.organization', 'o')
                        ->where('o.id = :orgId')
                        ->setParameter('orgId', $dto->organizationId);

                    $usersToNotify = $queryBuilder->getQuery()->getResult();
                    break;

                case 'ROLE':
                    if (!$dto->role) {
                        return $feedback->setErrorFlushDescription("Le niveau de publication (role) est requis pour le scope ROLE.")->autoInitFlush();
                    }
                    $role = strtoupper($dto->role);
                    if (!in_array($role, Role::values(), true)) {
                        return $feedback->setErrorFlushDescription("Niveau de publication invalide. Valeurs acceptées : " . implode(', ', Role::values()) . ".")->autoInitFlush();
                    }
                    $usersToNotify = $this->userRepository->findByRole($role);
                    break;

                case 'GLOBAL':
                    $this->securityService->checkPermission('ROLE_SUPER_ADMIN');
                    $usersToNotify = $this->userRepository->findAll();
                    break;

                default:
                    return $feedback->setErrorFlushDescription("Scope invalide. Valeurs acceptées : USER, ORGANIZATION, ROLE, GLOBAL.")->autoInitFlush();
            }

            if (empty($usersToNotify)) {
                return $feedback->setErrorFlushDescription("Aucun utilisateur cible trouvé pour cette notification.")->autoInitFlush();
            }

            $count = 0;
            foreach ($usersToNotify as $user) {
                $notification = $this->mapper->mapRequestToEntity($dto, $user);
                $this->entityManager->persist($notification);
                $createdNotifications[] = $notification;
                $count++;

                if (($count % 500) === 0) {
                    $this->entityManager->flush();
                }
            }

            $this->entityManager->flush();

            // Diffusion par email lorsque le canal est EMAIL (Mailpit en dev).
            $channel = is_string($dto->channel) ? ReminderChannel::tryFrom($dto->channel) : $dto->channel;
            if ($channel === ReminderChannel::EMAIL) {
                foreach ($createdNotifications as $notification) {
                    $this->notificationMailer->send($notification);
                }
            }

            $message = $count === 1 ? "Notification créée avec succès." : "$count notifications envoyées avec succès.";
            $feedback->setFlushDescription($message)->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    /**
     * Crée et persiste une notification destinée à un utilisateur précis
     * (usage interne : alarmes patient, rappels automatiques…).
     * L'email est envoyé si le canal est EMAIL.
     */
    public function createDirect(
        User $user,
        NotificationType $type,
        string $title,
        string $body,
        ReminderChannel $channel,
        ?string $relatedEntityType = null,
        ?string $relatedEntityId = null
    ): ?Notification {
        try {
            $notification = (new Notification())
                ->setUser($user)
                ->setType($type)
                ->setTitle($title)
                ->setBody($body)
                ->setChannel($channel)
                ->setReadAt(null)
                ->setRelatedEntityType($relatedEntityType)
                ->setRelatedEntityId($relatedEntityId);

            $this->entityManager->persist($notification);
            $this->entityManager->flush();

            if ($channel === ReminderChannel::EMAIL) {
                $this->notificationMailer->send($notification);
            }

            return $notification;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Marque une notification spécifique comme lue
     */
    public function markAsRead(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $notification = $this->repository->find($id);
            if (!$notification) {
                return $feedback->setErrorFlushDescription("Notification introuvable.")->autoInitFlush();
            }

            $notification->setReadAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            // refresh inutile, mais peut être conservé
            // $this->entityManager->refresh($notification);

            $feedback->setData($this->mapper->mapEntityToResponse($notification))
                ->setFlushDescription("Notification marquée comme lue avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $id, NotificationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $notification = $this->repository->find($id);
            if (!$notification) {
                return $feedback->setErrorFlushDescription("Notification introuvable.")->autoInitFlush();
            }

            $notification->setTitle($dto->title);
            $notification->setBody($dto->body);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($notification))
                ->setFlushDescription("Notification mise à jour avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $notification = $this->repository->find($id);
            if (!$notification) {
                return $feedback->setErrorFlushDescription("Notification introuvable.")->autoInitFlush();
            }

            $this->entityManager->remove($notification);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Notification supprimée avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
