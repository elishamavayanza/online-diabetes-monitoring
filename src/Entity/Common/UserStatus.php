<?php

namespace App\Entity\Common;

enum UserStatus: string
{
    case PENDING_ACTIVATION = 'PENDING_ACTIVATION';
    case ACTIVE = 'ACTIVE';
    case SUSPENDED = 'SUSPENDED';
    case DISABLED = 'DISABLED';
}
