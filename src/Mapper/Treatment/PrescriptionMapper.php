<?php

namespace App\Mapper\Treatment;

use App\DTO\Request\Treatment\PrescriptionRequestDTO;
use App\DTO\Response\Treatment\PrescriptionResponseDTO;
use App\Entity\Treatment\Prescription;
use App\Entity\Treatment\PrescriptionStatus;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\User;

class PrescriptionMapper
{
    public function mapRequestToEntity(
        PrescriptionRequestDTO $dto,
        Patient $patient,
        HealthcareProfessional $prescriber,
        HealthcareOrganization $organization,
        ?User $validatedBy = null,
        ?Prescription $prescription = null
    ): Prescription {
        $prescription ??= new Prescription();

        $prescription->setPatient($patient);
        $prescription->setPrescriber($prescriber);
        $prescription->setOrganization($organization);

        $startDate = $dto->startDate instanceof \DateTimeImmutable
            ? $dto->startDate
            : \DateTimeImmutable::createFromInterface($dto->startDate);
        $prescription->setStartDate($startDate);

        if ($dto->endDate !== null) {
            $endDate = $dto->endDate instanceof \DateTimeImmutable
                ? $dto->endDate
                : \DateTimeImmutable::createFromInterface($dto->endDate);
            $prescription->setEndDate($endDate);
        }

        if ($dto->status !== null) {
            $prescription->setStatus(is_string($dto->status) ? PrescriptionStatus::tryFrom($dto->status) : $dto->status);
        }

        $prescription->setNotes($dto->notes);
        $prescription->setValidatedAt($dto->validatedAt);
        $prescription->setValidatedBy($validatedBy);

        return $prescription;
    }

    public function mapEntityToResponse(Prescription $prescription): PrescriptionResponseDTO
    {
        return PrescriptionResponseDTO::fromEntity($prescription);
    }
}
