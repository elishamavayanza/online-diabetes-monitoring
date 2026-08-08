<?php

namespace App\Mapper\Patient;

use App\DTO\Request\Patient\AllergyRequestDTO;
use App\DTO\Response\Patient\AllergyResponseDTO;
use App\Entity\Identity\Patient;
use App\Entity\Patient\Allergy;

class AllergyMapper
{
    public function mapRequestToEntity(AllergyRequestDTO $dto, Patient $patient, ?Allergy $allergy = null): Allergy
    {
        $allergy ??= new Allergy();

        $allergy->setPatient($patient);
        $allergy->setName($dto->name);
        $allergy->setSeverity($dto->severity);
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
