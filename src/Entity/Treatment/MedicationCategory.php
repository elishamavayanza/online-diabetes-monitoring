<?php

namespace App\Entity\Treatment;

enum MedicationCategory: string
{
    case INSULIN = 'INSULIN';
    case TABLET = 'TABLET';
    case OTHER = 'OTHER';
}
