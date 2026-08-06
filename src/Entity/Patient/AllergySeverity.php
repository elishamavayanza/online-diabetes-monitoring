<?php

namespace App\Entity\Patient;

enum AllergySeverity: string
{
    case MILD = 'MILD';
    case MODERATE = 'MODERATE';
    case SEVERE = 'SEVERE';
}
