<?php

namespace App\Mapper\Patient;

use App\DTO\Request\Patient\MedicalConsentRequestDTO;
use App\DTO\Response\Patient\MedicalConsentResponseDTO;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use App\Entity\Patient\MedicalConsent;
use App\Entity\Patient\ConsentType;

class MedicalConsentMapper
{
    public function mapRequestToEntity(
        MedicalConsentRequestDTO $dto,
        Patient $patient,
        ?HealthcareOrganization $organization = null,
        ?User $createdBy = null,
        ?MedicalConsent $consent = null
    ): MedicalConsent {
        $isNew = $consent === null;
        $consent ??= new MedicalConsent();

        if ($isNew && $createdBy !== null) {
            $consent->setCreatedBy($createdBy);
        }

        $consent->setPatient($patient);
        $consent->setOrganization($organization);

        // Convertir la string du DTO en Enum ConsentType
        $consent->setConsentType(ConsentType::from($dto->consentType));

        $consent->setGrantedAt($dto->grantedAt);
        $consent->setRevokedAt($dto->revokedAt);

        // Note : Le documentFile est géré directement par le MedicalConsentService
        // via le FileUploaderService, on ne touche donc pas au documentUrl ici.

        return $consent;
    }

    public function mapEntityToResponse(MedicalConsent $consent): MedicalConsentResponseDTO
    {
        return MedicalConsentResponseDTO::fromEntity($consent);
    }
}
