<?php

namespace App\Service\Notification;

use App\DTO\Feedback;
use App\DTO\Request\Notification\NotificationRequestDTO;
use App\Entity\Identity\User;
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

                case 'GLOBAL':
                    $this->securityService->checkPermission('ROLE_SUPER_ADMIN');
                    $usersToNotify = $this->userRepository->findAll();
                    break;

                default:
                    return $feedback->setErrorFlushDescription("Scope invalide. Valeurs acceptées : USER, ORGANIZATION, GLOBAL.")->autoInitFlush();
            }

            if (empty($usersToNotify)) {
                return $feedback->setErrorFlushDescription("Aucun utilisateur cible trouvé pour cette notification.")->autoInitFlush();
            }

            $count = 0;
            foreach ($usersToNotify as $user) {
                $notification = $this->mapper->mapRequestToEntity($dto, $user);
                $this->entityManager->persist($notification);
                $count++;

                if (($count % 500) === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
            }

            $this->entityManager->flush();

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

            $notification->setIsRead(true);
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
