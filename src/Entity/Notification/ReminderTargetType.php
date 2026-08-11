<?php

namespace App\Entity\Notification;

/**
 * Représente le type de cible associé à une règle de rappel.
 */
enum ReminderTargetType: string
{
    /** Cible liée à la prise d'un médicament. */
    case MEDICATION = 'MEDICATION';

    /** Cible liée à un rendez-vous. */
    case APPOINTMENT = 'APPOINTMENT';

    /** Cible liée à une mesure médicale. */
    case MEASUREMENT = 'MEASUREMENT';
}
