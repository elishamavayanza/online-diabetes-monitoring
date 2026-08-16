<?php

namespace App\Mapper\Patient;

use App\DTO\Request\Patient\MedicalConsentRequestDTO;
use App\DTO\Response\Patient\MedicalConsentResponseDTO;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\Patient;
use App\Entity\Patient\MedicalConsent;
use App\Entity\Patient\ConsentType; // <-- Importez votre Enum

class MedicalConsentMapper
{
    public function mapRequestToEntity(
        MedicalConsentRequestDTO $dto,
        Patient $patient,
        ?HealthcareOrganization $organization = null,
        ?MedicalConsent $consent = null
    ): MedicalConsent {
        $consent ??= new MedicalConsent();

        $consent->setPatient($patient);
        $consent->setOrganization($organization);

        // Convertir la string du DTO en Enum ConsentType
        $consent->setConsentType(ConsentType::from($dto->consentType));

        $consent->setGrantedAt($dto->grantedAt);
        $consent->setRevokedAt($dto->revokedAt);
        $consent->setDocumentUrl($dto->documentUrl);

        return $consent;
    }

    public function mapEntityToResponse(MedicalConsent $consent): MedicalConsentResponseDTO
    {
        return MedicalConsentResponseDTO::fromEntity($consent);
    }
}
