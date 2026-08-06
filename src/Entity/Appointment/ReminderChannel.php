<?php

namespace App\Entity\Appointment;

enum ReminderChannel: string
{
    case EMAIL = 'EMAIL';
    case SMS = 'SMS';
    case PUSH = 'PUSH';
    case IN_APP = 'IN_APP';
}
