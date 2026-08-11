<?php

namespace App\Entity\Patient;

/**
 * Représente le niveau de sévérité d'une allergie.
 */
enum AllergySeverity: string
{
    /** Allergie légère. */
    case MILD = 'MILD';

    /** Allergie modérée. */
    case MODERATE = 'MODERATE';

    /** Allergie sévère. */
    case SEVERE = 'SEVERE';
}
