<?php

namespace App\Mapper\Patient;

use App\DTO\Request\Patient\EmergencyContactRequestDTO;
use App\DTO\Response\Patient\EmergencyContactResponseDTO;
use App\Entity\Identity\Patient;
use App\Entity\Patient\EmergencyContact;

class EmergencyContactMapper
{
    public function mapRequestToEntity(EmergencyContactRequestDTO $dto, Patient $patient, ?EmergencyContact $contact = null): EmergencyContact
    {
        $contact ??= new EmergencyContact();

        $contact->setPatient($patient);
        $contact->setFullName($dto->fullName);
        $contact->setRelationship($dto->relationship);
        $contact->setPhone($dto->phone);
        $contact->setEmail($dto->email);

        return $contact;
    }

    public function mapEntityToResponse(EmergencyContact $contact): EmergencyContactResponseDTO
    {
        return EmergencyContactResponseDTO::fromEntity($contact);
    }
}
