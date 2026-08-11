<?php

namespace App\Entity\Appointment;

/**
 * Représente les canaux disponibles pour l'envoi des rappels de rendez-vous aux utilisateurs.
 */
enum ReminderChannel: string
{
    /** Rappel envoyé par courrier électronique. */
    case EMAIL = 'EMAIL';

    /** Rappel envoyé par message texte (SMS). */
    case SMS = 'SMS';

    /** Notification push envoyée sur l'appareil mobile. */
    case PUSH = 'PUSH';

    /** Notification affichée directement au sein de l'application. */
    case IN_APP = 'IN_APP';
}
