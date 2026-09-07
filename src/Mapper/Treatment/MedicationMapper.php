<?php

namespace App\Mapper\Treatment;

use App\DTO\Request\Treatment\MedicationRequestDTO;
use App\DTO\Response\Treatment\MedicationResponseDTO;
use App\Entity\Treatment\Medication;
use App\Entity\Treatment\MedicationClass;
use App\Entity\Treatment\MedicationForm;
use InvalidArgumentException;

class MedicationMapper
{
    public function mapRequestToEntity(MedicationRequestDTO $dto, ?Medication $medication = null): Medication
    {
        $medication ??= new Medication();

        $medication->setName($dto->name);

        if ($dto->category !== null) {
            $categoryEnum = is_string($dto->category)
                ? MedicationClass::tryFrom($dto->category)
                : $dto->category;

            if ($categoryEnum === null) {
                throw new InvalidArgumentException(sprintf("La classe de médicament '%s' est invalide.", $dto->category));
            }

            $medication->setCategory($categoryEnum);
        }

        if ($medication->getCategory() === MedicationClass::GENERAL && $dto->form !== null) {
            $formEnum = is_string($dto->form)
                ? MedicationForm::tryFrom($dto->form)
                : $dto->form;

            if ($formEnum === null) {
                throw new InvalidArgumentException(sprintf("La forme de médicament '%s' est invalide.", $dto->form));
            }

            $medication->setForm($formEnum);
        }

        $medication->setDescription($dto->description);
        $medication->setManufacturer($dto->manufacturer);
        $medication->setActive($dto->active);

        return $medication;
    }

    public function mapEntityToResponse(Medication $medication): MedicationResponseDTO
    {
        return MedicationResponseDTO::fromEntity($medication);
    }
}
