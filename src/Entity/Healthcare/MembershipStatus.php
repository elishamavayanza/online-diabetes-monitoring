<?php

namespace App\Entity\Healthcare;

enum MembershipStatus: string
{
    case ACTIVE = 'ACTIVE';
    case SUSPENDED = 'SUSPENDED';
    case ENDED = 'ENDED';
}
