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

        // Gestion du rôle (convertit en Enum si c'est une string, ou garde l'objet)
        if ($dto->role !== null) {
            $role = is_string($dto->role)
                ? \App\Entity\Healthcare\TeamRole::from($dto->role) // Ajustez le namespace de l'Enum si besoin
                : $dto->role;
            $assignment->setRole($role);
        }

        // Conversion de startDate (DateTimeImmutable -> DateTime)
        if ($dto->startDate !== null) {
            $startDate = $dto->startDate instanceof \DateTimeImmutable
                ? \DateTime::createFromImmutable($dto->startDate)
                : ($dto->startDate instanceof \DateTime ? $dto->startDate : new \DateTime($dto->startDate));

            $assignment->setStartDate($startDate);
        }

        // Conversion de endDate (DateTimeImmutable -> DateTime)
        if ($dto->endDate !== null) {
            $endDate = $dto->endDate instanceof \DateTimeImmutable
                ? \DateTime::createFromImmutable($dto->endDate)
                : ($dto->endDate instanceof \DateTime ? $dto->endDate : new \DateTime($dto->endDate));

            $assignment->setEndDate($endDate);
        } else {
            $assignment->setEndDate(null);
        }

        if ($dto->active !== null) {
            $assignment->setActive($dto->active);
        }

        return $assignment;
    }

    public function mapEntityToResponse(CareTeamAssignment $assignment): CareTeamAssignmentResponseDTO
    {
        return CareTeamAssignmentResponseDTO::fromEntity($assignment);
    }
}
