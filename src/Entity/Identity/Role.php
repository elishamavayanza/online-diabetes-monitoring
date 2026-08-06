<?php

namespace App\Entity\Identity;

enum Role: string
{
    case ROLE_ROOT = 'ROLE_ROOT';
    case ROLE_ADMIN = 'ROLE_ADMIN';
    case ROLE_CLINICIAN = 'ROLE_CLINICIAN';
    case ROLE_NUTRITIONIST = 'ROLE_NUTRITIONIST';
    case ROLE_PATIENT = 'ROLE_PATIENT';
}
