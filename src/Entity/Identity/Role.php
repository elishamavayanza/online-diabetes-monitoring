<?php

namespace App\Entity\Identity;

/**
 * Représente les différents rôles de sécurité disponibles pour les utilisateurs.
 */
enum Role: string
{
    /** Rôle administrateur principal (root). */
    case ROLE_ROOT = 'ROLE_ROOT';

    /** Rôle administrateur standard. */
    case ROLE_ADMIN = 'ROLE_ADMIN';

    /** Rôle attribué aux cliniciens. */
    case ROLE_CLINICIAN = 'ROLE_CLINICIAN';

    /** Rôle attribué aux nutritionnistes. */
    case ROLE_NUTRITIONIST = 'ROLE_NUTRITIONIST';

    /** Rôle attribué aux patients. */
    case ROLE_PATIENT = 'ROLE_PATIENT';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
