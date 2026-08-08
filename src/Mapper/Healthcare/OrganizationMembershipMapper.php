<?php

namespace App\Mapper\Healthcare;

use App\DTO\Request\Healthcare\OrganizationMembershipRequestDTO;
use App\DTO\Response\Healthcare\OrganizationMembershipResponseDTO;
use App\Entity\Healthcare\OrganizationMembership;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Healthcare\HealthcareFacility;
use App\Entity\Healthcare\Department;
use App\Entity\Identity\User;

class OrganizationMembershipMapper
{
    public function mapRequestToEntity(
        OrganizationMembershipRequestDTO $dto,
        User $user,
        HealthcareOrganization $organization,
        ?HealthcareFacility $facility = null,
        ?Department $department = null,
        ?OrganizationMembership $membership = null
    ): OrganizationMembership {
        $membership ??= new OrganizationMembership();

        $membership->setUser($user);
        $membership->setOrganization($organization);
        $membership->setFacility($facility);
        $membership->setDepartment($department);
        $membership->setStartDate($dto->startDate);
        $membership->setEndDate($dto->endDate);
        $membership->setStatus($dto->status);

        return $membership;
    }

    public function mapEntityToResponse(OrganizationMembership $membership): OrganizationMembershipResponseDTO
    {
        return OrganizationMembershipResponseDTO::fromEntity($membership);
    }
}
