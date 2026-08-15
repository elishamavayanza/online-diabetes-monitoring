<?php

namespace App\Mapper\Patient;

use App\DTO\Request\Patient\AllergyRequestDTO;
use App\DTO\Response\Patient\AllergyResponseDTO;
use App\Entity\Identity\Patient;
use App\Entity\Patient\Allergy;
use App\Entity\Patient\AllergySeverity; // Assurez-vous d'importer la bonne enum

class AllergyMapper
{
    public function mapRequestToEntity(AllergyRequestDTO $dto, Patient $patient, ?Allergy $allergy = null): Allergy
    {
        $allergy ??= new Allergy();

        $allergy->setPatient($patient);
        $allergy->setName($dto->name);

        // Conversion de la string en Enum
        $severityEnum = AllergySeverity::tryFrom($dto->severity)
            ?? throw new \InvalidArgumentException(sprintf('Sévérité invalide : "%s"', $dto->severity));

        $allergy->setSeverity($severityEnum);

        $allergy->setReaction($dto->reaction);
        $allergy->setNotes($dto->notes);
        $allergy->setDiagnosedAt($dto->diagnosedAt);

        return $allergy;
    }

    public function mapEntityToResponse(Allergy $allergy): AllergyResponseDTO
    {
        return AllergyResponseDTO::fromEntity($allergy);
    }
}
