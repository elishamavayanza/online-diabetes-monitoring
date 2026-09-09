<?php

namespace App\Security;

use App\Entity\Common\UserStatus;
use App\Entity\Identity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Vérifie, à chaque requête API authentifiée (et à la connexion), que le
 * compte n'est pas suspendu et que son organisation n'est pas suspendue.
 * Un compte suspendu ne peut donc NI obtenir de JWT NI réutiliser un JWT
 * encore valide : il est redirigé vers l'écran de connexion (bloqué).
 */
class AccountStatusUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        $status = $user->getStatus();

        if ($status === UserStatus::SUSPENDED || $status === UserStatus::DISABLED) {
            throw new CustomUserMessageAuthenticationException(
                'Votre compte est suspendu. Contactez le support de votre organisation.'
            );
        }

        // Le ROOT n'est rattaché à aucune organisation.
        if (in_array('ROLE_ROOT', $user->getRoles(), true)) {
            return;
        }

        $memberships = $user->getOrganizationMemberships();
        if ($memberships->isEmpty()) {
            return;
        }

        $hasAccessibleOrganization = false;
        foreach ($memberships as $membership) {
            if (!$membership->getStatus()?->isActive()) {
                continue;
            }

            $organization = $membership->getOrganization();
            if ($organization !== null && $organization->isActive()) {
                $hasAccessibleOrganization = true;
                break;
            }
        }

        if (!$hasAccessibleOrganization) {
            throw new CustomUserMessageAuthenticationException(
                'Votre organisation est suspendue. Contactez le super administrateur de la plateforme.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Rien de particulier après authentification.
    }
}