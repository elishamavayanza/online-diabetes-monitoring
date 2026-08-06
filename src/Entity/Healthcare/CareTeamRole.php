<?php

namespace App\Entity\Healthcare;

enum CareTeamRole: string
{
    case PRIMARY_CLINICIAN = 'PRIMARY_CLINICIAN';
    case SPECIALIST = 'SPECIALIST';
    case NUTRITIONIST = 'NUTRITIONIST';
}
