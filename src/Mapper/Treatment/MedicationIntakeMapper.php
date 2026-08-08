<?php

namespace App\Mapper\Treatment;

use App\DTO\Request\Treatment\MedicationIntakeRequestDTO;
use App\DTO\Response\Treatment\MedicationIntakeResponseDTO;
use App\Entity\Treatment\MedicationIntake;
use App\Entity\Treatment\IntakeStatus;
use App\Entity\Treatment\PrescriptionItem;

class MedicationIntakeMapper
{
    public function mapRequestToEntity(MedicationIntakeRequestDTO $dto, PrescriptionItem $prescriptionItem, ?MedicationIntake $intake = null): MedicationIntake
    {
        $intake ??= new MedicationIntake();

        $intake->setPrescriptionItem($prescriptionItem);
        $intake->setTakenAt($dto->takenAt);
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
