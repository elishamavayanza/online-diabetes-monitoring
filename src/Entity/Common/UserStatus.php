<?php

namespace App\Entity\Common;

/**
 * Représente le statut ou l'état actuel d'un compte utilisateur dans le système.
 */
enum UserStatus: string
{
    /** Le compte est créé mais en attente d'activation (ex: validation d'email). */
    case PENDING_ACTIVATION = 'PENDING_ACTIVATION';

    /** Le compte est actif et pleinement opérationnel. */
    case ACTIVE = 'ACTIVE';

    /** Le compte a été temporairement suspendu (ex: suite à des infractions). */
    case SUSPENDED = 'SUSPENDED';

    /** Le compte a été désactivé ou fermé de manière définitive. */
    case DISABLED = 'DISABLED';
}
