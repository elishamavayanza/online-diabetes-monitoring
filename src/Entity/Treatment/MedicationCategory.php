<?php

namespace App\Entity\Treatment;

/**
 * Représente la catégorie d'un médicament.
 */
enum MedicationCategory: string
{
    /** Insuline ou produit injectable apparenté. */
    case INSULIN = 'INSULIN';

    /** Médicament en comprimés ou cachets. */
    case TABLET = 'TABLET';

    /** Autre type de médicament. */
    case OTHER = 'OTHER';
}
