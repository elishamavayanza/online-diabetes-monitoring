<?php

namespace App\Entity\Medical;

/**
 * Représente l'unité de mesure utilisée pour la glycémie.
 */
enum GlucoseUnit: string
{
    /** Milligrammes par décilitre (mg/dL). */
    case MG_DL = 'MG_DL';

    /** Millimoles par litre (mmol/L). */
    case MMOL_L = 'MMOL_L';
}
