<?php

namespace App\Entity\Patient;

enum ConsentType: string
{
    case DATA_PROCESSING = 'DATA_PROCESSING';
    case TELEMONITORING = 'TELEMONITORING';
    case DATA_SHARING_WITH_ORG = 'DATA_SHARING_WITH_ORG';
}
