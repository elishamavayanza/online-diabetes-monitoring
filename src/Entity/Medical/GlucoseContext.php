<?php

namespace App\Entity\Medical;

/**
 * Représente le contexte de mesure de la glycémie (moment de la journée ou par rapport aux repas).
 */
enum GlucoseContext: string
{
    /** À jeun. */
    case FASTING = 'FASTING';

    /** Avant un repas. */
    case BEFORE_MEAL = 'BEFORE_MEAL';

    /** Après un repas (post-prandial). */
    case AFTER_MEAL = 'AFTER_MEAL';

    /** Au coucher. */
    case BEDTIME = 'BEDTIME';

    /** Mesure aléatoire ou ponctuelle. */
    case RANDOM = 'RANDOM';
}
