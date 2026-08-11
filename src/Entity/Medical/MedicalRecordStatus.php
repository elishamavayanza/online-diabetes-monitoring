<?php

namespace App\Entity\Medical;

/**
 * Représente le statut d'un dossier médical (ouvert ou fermé).
 */
enum MedicalRecordStatus: string
{
    /** Le dossier médical est ouvert et actif. */
    case OPEN = 'OPEN';

    /** Le dossier médical est fermé ou archivé. */
    case CLOSED = 'CLOSED';
}
