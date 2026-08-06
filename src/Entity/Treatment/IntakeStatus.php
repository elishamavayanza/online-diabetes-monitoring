<?php

namespace App\Entity\Treatment;

enum IntakeStatus: string
{
    case TAKEN = 'TAKEN';
    case SKIPPED = 'SKIPPED';
    case DELAYED = 'DELAYED';
}
