<?php

namespace App\Observability;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Journalise les requêtes HTTP lentes (>= $thresholdMs) sur le canal
 * « performance » : route, statut, utilisateur et durée.
 */
final class RequestTimingListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ?TokenStorageInterface $tokenStorage = null,
        private readonly float $thresholdMs = 1000.0
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => 'onTerminate',
        ];
    }

    public function onTerminate(TerminateEvent $event): void
    {
        $request = $event->getRequest();
        $requestTimeFloat = $request->server->get('REQUEST_TIME_FLOAT');

        if ($request->isMethod('GET') && !$request->isXmlHttpRequest()) {
            // Les GET frontaux (SPA) ne sont pas mesurés ici.
        }

        $durationMs = (microtime(true) - $request->server->get('REQUEST_TIME_FLOAT', microtime(true))) * 1000.0;
        if ($durationMs < $this->thresholdMs) {
            return;
        }

        $userId = null;
        if ($this->tokenStorage !== null) {
            $token = $this->tokenStorage->getToken();
            $userId = $token?->getUserIdentifier();
        }

        $this->logger->warning(
            'Requête HTTP lente ({duration_ms} ms) : {method} {uri} ({status}, user: {userId})',
            [
                'duration_ms' => round($durationMs, 1),
                'method' => $request->getMethod(),
                'uri' => $request->getRequestUri(),
                'status' => $event->getResponse()?->getStatusCode(),
                'userId' => $userId,
            ]
        );
    }
}