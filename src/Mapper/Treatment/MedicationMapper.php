<?php

namespace App\Mapper\Treatment;

use App\DTO\Request\Treatment\MedicationRequestDTO;
use App\DTO\Response\Treatment\MedicationResponseDTO;
use App\Entity\Treatment\Medication;
use App\Entity\Treatment\MedicationCategory;

class MedicationMapper
{
    public function mapRequestToEntity(MedicationRequestDTO $dto, ?Medication $medication = null): Medication
    {
        $medication ??= new Medication();

        $medication->setName($dto->name);

        if ($dto->category !== null) {
            $medication->setCategory(is_string($dto->category) ? MedicationCategory::tryFrom($dto->category) : $dto->category);
        }

        $medication->setDescription($dto->description);
        $medication->setInsulinLevel($dto->insulinLevel);
        $medication->setManufacturer($dto->manufacturer);

        return $medication;
    }

    public function mapEntityToResponse(Medication $medication): MedicationResponseDTO
    {
        return MedicationResponseDTO::fromEntity($medication);
    }
}
