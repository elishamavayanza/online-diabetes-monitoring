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

        // Conversion sécurisée de startDate
        if ($dto->startDate !== null) {
            $startDate = ($dto->startDate instanceof \DateTimeInterface)
                ? \DateTimeImmutable::createFromInterface($dto->startDate)
                : new \DateTimeImmutable($dto->startDate);
            $prescription->setStartDate($startDate);
        }

        // Conversion sécurisée de endDate
        if ($dto->endDate !== null) {
            $endDate = ($dto->endDate instanceof \DateTimeInterface)
                ? \DateTimeImmutable::createFromInterface($dto->endDate)
                : new \DateTimeImmutable($dto->endDate);
            $prescription->setEndDate($endDate);
        }

        // Conversion sécurisée de validatedAt
        if ($dto->validatedAt !== null) {
            $validatedAt = ($dto->validatedAt instanceof \DateTimeInterface)
                ? \DateTimeImmutable::createFromInterface($dto->validatedAt)
                : new \DateTimeImmutable($dto->validatedAt);
            $prescription->setValidatedAt($validatedAt);
        }

        if ($dto->status !== null) {
            $status = is_string($dto->status) ? PrescriptionStatus::tryFrom($dto->status) : $dto->status;
            // On s'assure que le statut n'est pas null avant de l'assigner
            if ($status instanceof PrescriptionStatus) {
                $prescription->setStatus($status);
            }
        }

        $prescription->setNotes($dto->notes);
        $prescription->setValidatedBy($validatedBy);

        return $prescription;
    }

    public function mapEntityToResponse(Prescription $prescription): PrescriptionResponseDTO
    {
        return PrescriptionResponseDTO::fromEntity($prescription);
    }
}
