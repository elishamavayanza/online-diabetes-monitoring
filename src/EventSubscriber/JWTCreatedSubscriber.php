<?php

namespace App\EventSubscriber;

use App\Entity\Identity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTCreatedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onJWTCreated',
        ];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $payload = $event->getData();

        // Ajout des informations personnalisées
        $payload['id'] = $user->getId();
        $payload['fullname'] = $user->getFullName();
        $payload['roles'] = $user->getRoles();

        // Si vous avez une méthode getPermissions() dans votre entité ou votre logique métier
        // $payload['permissions'] = $user->getPermissions();

        $event->setData($payload);
    }
}
