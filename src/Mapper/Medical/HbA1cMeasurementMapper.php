<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\HbA1cMeasurementRequestDTO;
use App\DTO\Response\Medical\HbA1cMeasurementResponseDTO;
use App\Entity\Medical\HbA1cMeasurement;
use App\Entity\Identity\Patient;

class HbA1cMeasurementMapper
{
    public function mapRequestToEntity(HbA1cMeasurementRequestDTO $dto, Patient $patient, ?HbA1cMeasurement $measurement = null): HbA1cMeasurement
    {
        $measurement ??= new HbA1cMeasurement();

        $measurement->setPatient($patient);
        $measurement->setValuePercent($dto->valuePercent);

        return $measurement;
    }

    public function mapEntityToResponse(HbA1cMeasurement $measurement): HbA1cMeasurementResponseDTO
    {
        return HbA1cMeasurementResponseDTO::fromEntity($measurement);
    }
}
