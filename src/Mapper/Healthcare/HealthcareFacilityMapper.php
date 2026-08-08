<?php

namespace App\Mapper\Healthcare;

use App\DTO\Request\Healthcare\HealthcareFacilityRequestDTO;
use App\DTO\Response\Healthcare\HealthcareFacilityResponseDTO;
use App\Entity\Healthcare\HealthcareFacility;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\Address;

class HealthcareFacilityMapper
{
    public function mapRequestToEntity(HealthcareFacilityRequestDTO $dto, HealthcareOrganization $organization, ?HealthcareFacility $facility = null): HealthcareFacility
    {
        $facility ??= new HealthcareFacility();

        $facility->setOrganization($organization);
        $facility->setName($dto->name);
        $facility->setPhone($dto->phone);

        if (!empty($dto->address)) {
            $address = new Address(
                street: $dto->address['street'] ?? null,
                city: $dto->address['city'] ?? null,
                postalCode: $dto->address['postalCode'] ?? null,
                country: $dto->address['country'] ?? null,
                state: $dto->address['state'] ?? null
            );
            $facility->setAddress($address);
        }

        return $facility;
    }

    public function mapEntityToResponse(HealthcareFacility $facility): HealthcareFacilityResponseDTO
    {
        return HealthcareFacilityResponseDTO::fromEntity($facility);
    }
}
