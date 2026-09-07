<?php

namespace App\Entity\Treatment;

/**
 * Représente le type d'une insuline en fonction de sa durée et vitesse d'action.
 */
enum InsulinType: string
{
    /** Insuline à action rapide (début d'action en quelques minutes). */
    case RAPID_ACTING = 'RAPID_ACTING';

    /** Insuline à action courte (début d'action en 30 minutes environ). */
    case SHORT_ACTING = 'SHORT_ACTING';

    /** Insuline à action intermédiaire (durée d'action moyenne). */
    case INTERMEDIATE_ACTING = 'INTERMEDIATE_ACTING';

    /** Insuline à action longue (effet sur une journée entière). */
    case LONG_ACTING = 'LONG_ACTING';

    /** Insuline prémélangée (mélange d'insulines rapide et basale). */
    case MIXED = 'MIXED';

    /** Autre type d'insuline. */
    case OTHER = 'OTHER';
}
