<?php

namespace App\Entity\Treatment;

/**
 * Représente le site d'injection d'une injection d'insuline.
 */
enum InjectionSite: string
{
    /** L' abdomen. */
    case ABDOMEN = 'ABDOMEN';

    /** La cuisse. */
    case THIGH = 'THIGH';

    /** La partie supérieure du bras. */
    case UPPER_ARM = 'UPPER_ARM';

    /** La fesse. */
    case BUTTOCK = 'BUTTOCK';

    /** Autre site d'injection. */
    case OTHER = 'OTHER';
}
