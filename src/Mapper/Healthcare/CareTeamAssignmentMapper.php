<?php

namespace App\Mapper\Healthcare;

use App\DTO\Request\Healthcare\CareTeamAssignmentRequestDTO;
use App\DTO\Response\Healthcare\CareTeamAssignmentResponseDTO;
use App\Entity\Healthcare\CareTeamAssignment;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Healthcare\HealthcareOrganization;

class CareTeamAssignmentMapper
{
    public function mapRequestToEntity(
        CareTeamAssignmentRequestDTO $dto,
        Patient $patient,
        HealthcareProfessional $professional,
        HealthcareOrganization $organization,
        ?CareTeamAssignment $assignment = null
    ): CareTeamAssignment {
        $assignment ??= new CareTeamAssignment();

        $assignment->setPatient($patient);
        $assignment->setProfessional($professional);
        $assignment->setOrganization($organization);
        $assignment->setRole($dto->role);
        $assignment->setStartDate($dto->startDate);
        $assignment->setEndDate($dto->endDate);
        $assignment->setActive($dto->active);

        return $assignment;
    }

    public function mapEntityToResponse(CareTeamAssignment $assignment): CareTeamAssignmentResponseDTO
    {
        return CareTeamAssignmentResponseDTO::fromEntity($assignment);
    }
}
