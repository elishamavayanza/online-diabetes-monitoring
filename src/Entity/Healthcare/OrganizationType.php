<?php

namespace App\Entity\Healthcare;

/**
 * Représente le type ou la catégorie d'une organisation de santé.
 */
enum OrganizationType: string
{
    /** Centre hospitalier ou grand hôpital. */
    case HOSPITAL = 'HOSPITAL';

    /** Clinique ou centre médical de proximité. */
    case CLINIC = 'CLINIC';

    /** Réseau de santé ou groupement d'établissements. */
    case NETWORK = 'NETWORK';
}
