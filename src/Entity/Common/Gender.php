<?php

namespace App\Entity\Common;

/**
 * Représente le genre ou le sexe d'une personne dans le système.
 */
enum Gender: string
{
    /** Sexe masculin. */
    case MALE = 'MALE';

    /** Sexe féminin. */
    case FEMALE = 'FEMALE';

    /** Autre genre. */
    case OTHER = 'OTHER';

    /** Genre non spécifié ou non renseigné. */
    case UNSPECIFIED = 'UNSPECIFIED';
}
