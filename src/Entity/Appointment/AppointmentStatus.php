<?php

namespace App\Entity\Appointment;

/**
 * Représente les différents états possibles d'un rendez-vous médical dans le système.
 */
enum AppointmentStatus: string
{
    /** Le rendez-vous a été planifié mais pas encore confirmé. */
    case SCHEDULED = 'SCHEDULED';

    /** Le rendez-vous a été confirmé (par le patient ou le cabinet). */
    case CONFIRMED = 'CONFIRMED';

    /** Le rendez-vous a eu lieu et est terminé. */
    case COMPLETED = 'COMPLETED';

    /** Le rendez-vous a été annulé. */
    case CANCELLED = 'CANCELLED';

    /** Le patient ne s'est pas présenté au rendez-vous (lapin / no-show). */
    case NO_SHOW = 'NO_SHOW';

    /** Le report du rendez-vous a été demandé. */
    case RESCHEDULE_REQUESTED = 'RESCHEDULE_REQUESTED';
}
