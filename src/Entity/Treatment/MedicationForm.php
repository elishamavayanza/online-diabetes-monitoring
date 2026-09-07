<?php

namespace App\Entity\Treatment;

/**
 * Représente la forme galénique d'un médicament général.
 *
 * Réservé aux médicaments de classe `GENERAL` (les insulines ont leur propre profil).
 */
enum MedicationForm: string
{
    /** Comprimé. */
    case TABLET = 'TABLET';

    /** Liquide (sirop, suspension, solution buvable). */
    case LIQUID = 'LIQUID';
}