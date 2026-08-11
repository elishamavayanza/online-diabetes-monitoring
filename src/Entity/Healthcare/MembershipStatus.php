<?php

namespace App\Entity\Healthcare;

/**
 * Représente le statut d'une adhésion ou relation d'un utilisateur au sein d'une organisation de santé.
 */
enum MembershipStatus: string
{
    /** L'adhésion est active et en cours de validité. */
    case ACTIVE = 'ACTIVE';

    /** L'adhésion est temporairement suspendue. */
    case SUSPENDED = 'SUSPENDED';

    /** L'adhésion est terminée ou révoquée. */
    case ENDED = 'ENDED';
}
