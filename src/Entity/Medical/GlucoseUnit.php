<?php

namespace App\Entity\Medical;

enum GlucoseUnit: string
{
    case MG_DL = 'MG_DL';
    case MMOL_L = 'MMOL_L';
}
