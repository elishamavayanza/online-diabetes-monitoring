<?php

namespace App\Mapper\Treatment;

use App\DTO\Request\Treatment\MedicationIntakeRequestDTO;
use App\DTO\Response\Treatment\MedicationIntakeResponseDTO;
use App\Entity\Treatment\MedicationIntake;
use App\Entity\Treatment\IntakeStatus;
use App\Entity\Treatment\PrescriptionItem;
use App\Entity\Identity\Patient;
use App\Entity\Identity\User;

class MedicationIntakeMapper
{
    public function mapRequestToEntity(MedicationIntakeRequestDTO $dto, PrescriptionItem $prescriptionItem, Patient $patient, User $issuer, ?MedicationIntake $intake = null): MedicationIntake
    {
        $intake ??= new MedicationIntake();

        $intake->setPatient($patient);
        $intake->setIssuer($issuer);
        $intake->setPrescriptionItem($prescriptionItem);
        $intake->setTakenAt($dto->takenAt);
        $intake->setMeasuredAt($dto->takenAt);
        $intake->setQuantityTaken($dto->quantityTaken);

        if ($dto->status !== null) {
            $intake->setStatus(is_string($dto->status) ? IntakeStatus::tryFrom($dto->status) : $dto->status);
        }

        return $intake;
    }

    public function mapEntityToResponse(MedicationIntake $intake): MedicationIntakeResponseDTO
    {
        return MedicationIntakeResponseDTO::fromEntity($intake);
    }
}
