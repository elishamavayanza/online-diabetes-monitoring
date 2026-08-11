<?php

namespace App\Entity\Healthcare;

/**
 * Représente le rôle d'un professionnel de santé au sein d'une équipe de soins.
 */
enum CareTeamRole: string
{
    /** Médecin traitant ou clinicien principal en charge du patient. */
    case PRIMARY_CLINICIAN = 'PRIMARY_CLINICIAN';

    /** Médecin spécialiste consulté pour un domaine médical particulier. */
    case SPECIALIST = 'SPECIALIST';

    /** Nutritionniste ou diététicien participant au suivi diététique. */
    case NUTRITIONIST = 'NUTRITIONIST';
}
