<?php

namespace App\Entity\Treatment;

/**
 * Représente la classe d'un médicament.
 *
 * Deux grandes classes sont distinguées dans le référentiel:
 * - `INSULIN` : médicament antidiabétique à base d'insuline (avec un profil d'insuline associé).
 * - `GENERAL` : tout autre médicament (comprimés, autres formes).
 */
enum MedicationClass: string
{
    /** Insuline ou produit injectable apparenté. */
    case INSULIN = 'INSULIN';

    /** Médicament général (comprimé, autre). */
    case GENERAL = 'GENERAL';
}