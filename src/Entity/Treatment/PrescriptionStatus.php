<?php

namespace App\Entity\Treatment;

/**
 * Représente les différents statuts possibles d'une prescription médicale.
 */
enum PrescriptionStatus: string
{
    /** La prescription est en cours de rédaction (brouillon). */
    case DRAFT = 'DRAFT';

    /** La prescription est active et en cours. */
    case ACTIVE = 'ACTIVE';

    /** La prescription est terminée. */
    case COMPLETED = 'COMPLETED';

    /** La prescription a été annulée. */
    case CANCELLED = 'CANCELLED';
}
