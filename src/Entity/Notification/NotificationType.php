<?php

namespace App\Entity\Notification;

/**
 * Représente les différents types de notifications système pouvant être envoyées.
 */
enum NotificationType: string
{
    /** Rappel de prise de médicament. */
    case MEDICATION_REMINDER = 'MEDICATION_REMINDER';

    /** Rappel de rendez-vous médical. */
    case APPOINTMENT_REMINDER = 'APPOINTMENT_REMINDER';

    /** Rappel de prise de mesure (glycémie, tension, poids, etc.). */
    case MEASUREMENT_REMINDER = 'MEASUREMENT_REMINDER';

    /** Réception d'un nouveau message. */
    case MESSAGE_RECEIVED = 'MESSAGE_RECEIVED';

    /** Mise à jour d'une ordonnance. */
    case PRESCRIPTION_UPDATED = 'PRESCRIPTION_UPDATED';

    /** Alerte ou notification générale du système. */
    case SYSTEM_ALERT = 'SYSTEM_ALERT';
}
