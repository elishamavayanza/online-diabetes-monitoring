<?php

namespace App\Mapper\Treatment;

use App\DTO\Request\Treatment\MedicationRequestDTO;
use App\DTO\Response\Treatment\MedicationResponseDTO;
use App\Entity\Treatment\Medication;
use App\Entity\Treatment\MedicationCategory;
use InvalidArgumentException;

class MedicationMapper
{
    public function mapRequestToEntity(MedicationRequestDTO $dto, ?Medication $medication = null): Medication
    {
        $medication ??= new Medication();

        $medication->setName($dto->name);

        if ($dto->category !== null) {
            $categoryEnum = is_string($dto->category)
                ? MedicationCategory::tryFrom($dto->category)
                : $dto->category;

            if ($categoryEnum === null) {
                throw new InvalidArgumentException(sprintf("La catégorie de médicament '%s' est invalide.", $dto->category));
            }

            $medication->setCategory($categoryEnum);
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
