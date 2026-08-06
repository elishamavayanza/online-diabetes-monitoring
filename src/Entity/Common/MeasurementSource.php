<?php

namespace App\Entity\Common;

enum MeasurementSource: string
{
    case MANUAL_ENTRY = 'MANUAL_ENTRY';
    case CONNECTED_DEVICE = 'CONNECTED_DEVICE';
    case IMPORTED = 'IMPORTED';
    case CLINICIAN_ENTRY = 'CLINICIAN_ENTRY';
}
