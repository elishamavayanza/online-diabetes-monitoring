<?php

namespace App\Mapper\Treatment;

use App\DTO\Request\Treatment\InsulinRequestDTO;
use App\DTO\Response\Treatment\InsulinResponseDTO;
use App\Entity\Treatment\Insulin;
use App\Entity\Treatment\InsulinType;
use App\Entity\Treatment\Medication;

class InsulinMapper
{
    public function mapRequestToEntity(InsulinRequestDTO $dto, Medication $medication, ?Insulin $insulin = null): Insulin
    {
        $insulin ??= new Insulin();

        $insulin->setMedication($medication);

        if ($dto->insulinType !== null) {
            $insulin->setInsulinType(is_string($dto->insulinType) ? InsulinType::tryFrom($dto->insulinType) : $dto->insulinType);
        }

        $insulin->setConcentration($dto->concentration);

        return $insulin;
    }

    public function mapEntityToResponse(Insulin $insulin): InsulinResponseDTO
    {
        return InsulinResponseDTO::fromEntity($insulin);
    }
}