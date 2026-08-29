<?php

namespace App\Mapper\Healthcare;

use App\DTO\Request\Healthcare\HealthcareOrganizationRequestDTO;
use App\DTO\Response\Healthcare\HealthcareOrganizationResponseDTO;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\Address;

class HealthcareOrganizationMapper
{
    public function mapRequestToEntity(HealthcareOrganizationRequestDTO $dto, ?HealthcareOrganization $organization = null): HealthcareOrganization
    {
        $organization ??= new HealthcareOrganization();

        $organization->setName($dto->name);
        $organization->setShortName($dto->shortName);
        if ($dto->type) {
            $organization->setType(\App\Entity\Healthcare\OrganizationType::from($dto->type));
        }
        $organization->setEmail($dto->email);
        $organization->setPhone($dto->phone);
        $organization->setWebsite($dto->website);
        // Supprimé : $organization->setLogoUrl($dto->logoUrl); -> Géré par le Service via l'uploader
        $organization->setActive($dto->active);

        if (!empty($dto->address)) {
            $address = new Address();
            $address->setStreet($dto->address['street'] ?? null);
            $address->setCity($dto->address['city'] ?? null);
            $address->setPostalCode($dto->address['postalCode'] ?? null);
            $address->setCountry($dto->address['country'] ?? null);
            $address->setState($dto->address['state'] ?? null);

            $organization->setAddress($address);
        }

        return $organization;
    }

    public function mapEntityToResponse(HealthcareOrganization $organization): HealthcareOrganizationResponseDTO
    {
        return HealthcareOrganizationResponseDTO::fromEntity($organization);
    }
}
