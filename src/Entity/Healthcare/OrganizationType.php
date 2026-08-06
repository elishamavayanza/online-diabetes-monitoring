<?php

namespace App\Entity\Healthcare;

enum OrganizationType: string
{
    case HOSPITAL = 'HOSPITAL';
    case CLINIC = 'CLINIC';
    case NETWORK = 'NETWORK';
}
