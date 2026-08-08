<?php

namespace App\Mapper\Patient;

use App\DTO\Request\Patient\MedicalConsentRequestDTO;
use App\DTO\Response\Patient\MedicalConsentResponseDTO;
use App\Entity\Identity\Patient;
use App\Entity\Organization\Organization;
use App\Entity\Patient\MedicalConsent;

class MedicalConsentMapper
{
    public function mapRequestToEntity(
        MedicalConsentRequestDTO $dto,
        Patient $patient,
        ?Organization $organization = null,
        ?MedicalConsent $consent = null
    ): MedicalConsent {
        $consent ??= new MedicalConsent();

        $consent->setPatient($patient);
        $consent->setOrganization($organization);
        $consent->setConsentType($dto->consentType);
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
