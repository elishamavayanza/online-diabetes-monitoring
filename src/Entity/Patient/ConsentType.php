<?php

namespace App\Entity\Patient;

/**
 * Représente les différents types de consentements qu'un patient peut accorder.
 */
enum ConsentType: string
{
    /** Consentement au traitement des données personnelles et de santé. */
    case DATA_PROCESSING = 'DATA_PROCESSING';

    /** Consentement au télémonitoring et à la télésurveillance médicale. */
    case TELEMONITORING = 'TELEMONITORING';

    /** Consentement au partage des données avec des organisations partenaires. */
    case DATA_SHARING_WITH_ORG = 'DATA_SHARING_WITH_ORG';
}
