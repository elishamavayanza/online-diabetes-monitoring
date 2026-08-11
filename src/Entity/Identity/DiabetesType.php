<?php

namespace App\Entity\Identity;

/**
 * Représente le type de diabète pour le suivi d'un patient.
 */
enum DiabetesType: string
{
    /** Diabète de type 1 (insulinodépendant). */
    case TYPE_1 = 'TYPE_1';

    /** Diabète de type 2 (non insulinodépendant). */
    case TYPE_2 = 'TYPE_2';

    /** Diabète gestationnel (survenant pendant la grossesse). */
    case GESTATIONAL = 'GESTATIONAL';

    /** Autre type de diabète. */
    case OTHER = 'OTHER';
}
