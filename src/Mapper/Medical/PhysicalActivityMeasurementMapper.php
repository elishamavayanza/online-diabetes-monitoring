<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\PhysicalActivityMeasurementRequestDTO;
use App\DTO\Response\Medical\PhysicalActivityMeasurementResponseDTO;
use App\Entity\Medical\PhysicalActivityMeasurement;
use App\Entity\Identity\Patient;

class PhysicalActivityMeasurementMapper
{
    public function mapRequestToEntity(PhysicalActivityMeasurementRequestDTO $dto, Patient $patient, ?PhysicalActivityMeasurement $measurement = null): PhysicalActivityMeasurement
    {
        $measurement ??= new PhysicalActivityMeasurement();

        $measurement->setPatient($patient);
        $measurement->setActivityType($dto->activityType);
        $measurement->setDurationMinutes($dto->durationMinutes);
        $measurement->setCaloriesBurned($dto->caloriesBurned);
        $measurement->setMinHeartRate($dto->minHeartRate);
        $measurement->setMaxHeartRate($dto->maxHeartRate);

        return $measurement;
    }

    public function mapEntityToResponse(PhysicalActivityMeasurement $measurement): PhysicalActivityMeasurementResponseDTO
    {
        return PhysicalActivityMeasurementResponseDTO::fromEntity($measurement);
    }
}
