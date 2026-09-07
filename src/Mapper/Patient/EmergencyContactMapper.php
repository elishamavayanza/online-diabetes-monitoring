<?php

namespace App\Mapper\Patient;

use App\DTO\Request\Patient\EmergencyContactRequestDTO;
use App\DTO\Response\Patient\EmergencyContactResponseDTO;
use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use App\Entity\Patient\EmergencyContact;

class EmergencyContactMapper
{
    public function mapRequestToEntity(EmergencyContactRequestDTO $dto, Patient $patient, ?User $createdBy = null, ?EmergencyContact $contact = null): EmergencyContact
    {
        $isNew = $contact === null;
        $contact ??= new EmergencyContact();

        if ($isNew && $createdBy !== null) {
            $contact->setCreatedBy($createdBy);
        }

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
