<?php

namespace App\Entity\Identity;

/**
 * Représente le type de professionnel de santé au sein du système.
 */
enum ProfessionalType: string
{
    /** Clinicien ou médecin traitant. */
    case CLINICIAN = 'CLINICIAN';

    /** Nutritionniste ou diététicien. */
    case NUTRITIONIST = 'NUTRITIONIST';
}
