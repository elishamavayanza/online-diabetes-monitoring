<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\WeightMeasurementRequestDTO;
use App\DTO\Response\Medical\WeightMeasurementResponseDTO;
use App\Entity\Medical\WeightMeasurement;
use App\Entity\Identity\Patient;

class WeightMeasurementMapper
{
    public function mapRequestToEntity(WeightMeasurementRequestDTO $dto, Patient $patient, ?WeightMeasurement $measurement = null): WeightMeasurement
    {
        $measurement ??= new WeightMeasurement();

        $measurement->setPatient($patient);
        $measurement->setValueKg($dto->valueKg);
        $measurement->setHeightCm($dto->heightCm);

        return $measurement;
    }

    public function mapEntityToResponse(WeightMeasurement $measurement): WeightMeasurementResponseDTO
    {
        return WeightMeasurementResponseDTO::fromEntity($measurement);
    }
}
