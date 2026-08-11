<?php

namespace App\Entity\Treatment;

/**
 * Représente le statut de prise d'un traitement ou d'un médicament.
 */
enum IntakeStatus: string
{
    /** Le médicament a été pris. */
    case TAKEN = 'TAKEN';

    /** La prise a été sautée ou oubliée. */
    case SKIPPED = 'SKIPPED';

    /** La prise a été retardée. */
    case DELAYED = 'DELAYED';
}
