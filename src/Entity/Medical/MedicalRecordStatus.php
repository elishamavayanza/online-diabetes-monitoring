<?php

namespace App\Entity\Medical;

enum MedicalRecordStatus: string
{
    case OPEN = 'OPEN';
    case CLOSED = 'CLOSED';
}
