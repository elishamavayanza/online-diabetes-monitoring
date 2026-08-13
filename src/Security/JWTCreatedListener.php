<?php

namespace App\Security;

use App\Entity\Identity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        // Récupérer les données personnalisées (ex: les organisations liées)
        $organizations = [];
        foreach ($user->getOrganizationMemberships() as $membership) {
            if ($membership->getStatus()?->value === 'ACTIVE') {
                $organizations[] = [
                    'organization_id' => $membership->getOrganization()?->getId(),
                    'organization_name' => $membership->getOrganization()?->getName(),
                    'role' => $membership->getStatus()?->value,
                ];
            }
        }

        // Récupérer le payload actuel
        $payload = $event->getData();

        // Ajouter les informations supplémentaires
        $payload['fullName'] = $user->getFullName();
        $payload['email'] = $user->getEmail();
        $payload['organizations'] = $organizations;

        // Réinjecter le payload modifié
        $event->setData($payload);
    }
}
