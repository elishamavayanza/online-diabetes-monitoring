<?php

namespace App\Entity\Appointment;

enum AppointmentStatus: string
{
    case SCHEDULED = 'SCHEDULED';
    case CONFIRMED = 'CONFIRMED';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
    case NO_SHOW = 'NO_SHOW';
}
