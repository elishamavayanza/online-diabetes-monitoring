<?php

namespace App\Entity\Notification;

enum ReminderTargetType: string
{
    case MEDICATION = 'MEDICATION';
    case APPOINTMENT = 'APPOINTMENT';
    case MEASUREMENT = 'MEASUREMENT';
}
