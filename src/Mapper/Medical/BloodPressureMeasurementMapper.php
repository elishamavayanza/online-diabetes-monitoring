<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\BloodPressureMeasurementRequestDTO;
use App\DTO\Response\Medical\BloodPressureMeasurementResponseDTO;
use App\Entity\Medical\BloodPressureMeasurement;
use App\Entity\Identity\Patient;

class BloodPressureMeasurementMapper
{
    public function mapRequestToEntity(BloodPressureMeasurementRequestDTO $dto, Patient $patient, ?BloodPressureMeasurement $measurement = null): BloodPressureMeasurement
    {
        $measurement ??= new BloodPressureMeasurement();

        $measurement->setPatient($patient);
        $measurement->setSystolic($dto->systolic);
        $measurement->setDiastolic($dto->diastolic);
        $measurement->setPulse($dto->pulse);

        return $measurement;
    }

    public function mapEntityToResponse(BloodPressureMeasurement $measurement): BloodPressureMeasurementResponseDTO
    {
        return BloodPressureMeasurementResponseDTO::fromEntity($measurement);
    }
}
