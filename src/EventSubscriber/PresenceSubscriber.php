<?php

namespace App\EventSubscriber;

use App\Entity\Identity\User;
use App\Service\Common\PresenceService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Enregistre un souffle d'activité pour l'utilisateur authentifié à chaque
 * requête API (voir PresenceService). Alimente la présence affichée dans la
 * messagerie (GET /api/users/{id}/presence).
 */
class PresenceSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly PresenceService $presenceService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::FINISH_REQUEST => 'onFinishRequest',
        ];
    }

    public function onFinishRequest(FinishRequestEvent $event): void
    {
        $path = $event->getRequest()->getPathInfo();

        if (!str_starts_with($path, '/api')) {
            return;
        }

        // On exclut la documentation API ainsi que le polling de présence
        // lui-même : sinon un simple onglet ouvert sur la messagerie
        // garderait l'utilisateur « en ligne » en continu.
        if (str_starts_with($path, '/api/doc') || str_contains($path, '/presence')) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->presenceService->recordActivity($user);
    }
}