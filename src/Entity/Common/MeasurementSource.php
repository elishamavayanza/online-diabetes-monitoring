<?php

namespace App\Entity\Common;

/**
 * Représente la source ou l'origine d'une mesure ou d'une opération médicale.
 */
enum MeasurementSource: string
{
    /** Saisie manuelle par l'utilisateur ou le patient. */
    case MANUAL_ENTRY = 'MANUAL_ENTRY';

    /** Donnée provenant d'un appareil connecté ou d'un capteur biométrique. */
    case CONNECTED_DEVICE = 'CONNECTED_DEVICE';

    /** Donnée importée à partir d'un fichier externe ou d'un autre système. */
    case IMPORTED = 'IMPORTED';

    /** Saisie réalisée directement par un professionnel de santé. */
    case CLINICIAN_ENTRY = 'CLINICIAN_ENTRY';
}
