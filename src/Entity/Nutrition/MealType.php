<?php

namespace App\Entity\Nutrition;

/**
 * Représente les différents types de repas de la journée.
 */
enum MealType: string
{
    /** Petit-déjeuner. */
    case BREAKFAST = 'BREAKFAST';

    /** Déjeuner. */
    case LUNCH = 'LUNCH';

    /** Dîner. */
    case DINNER = 'DINNER';

    /** Collation ou en-cas. */
    case SNACK = 'SNACK';
}
