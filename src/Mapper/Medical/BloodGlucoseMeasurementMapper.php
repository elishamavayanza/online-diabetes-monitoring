<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\BloodGlucoseMeasurementRequestDTO;
use App\DTO\Response\Medical\BloodGlucoseMeasurementResponseDTO;
use App\Entity\Medical\BloodGlucoseMeasurement;
use App\Entity\Medical\GlucoseContext;
use App\Entity\Medical\GlucoseUnit;
use App\Entity\Identity\Patient;

class BloodGlucoseMeasurementMapper
{
    public function mapRequestToEntity(BloodGlucoseMeasurementRequestDTO $dto, Patient $patient, ?BloodGlucoseMeasurement $measurement = null): BloodGlucoseMeasurement
    {
        $measurement ??= new BloodGlucoseMeasurement();

        $measurement->setPatient($patient);
        $measurement->setValue($dto->value);

        if ($dto->unit !== null) {
            $measurement->setUnit(is_string($dto->unit) ? GlucoseUnit::tryFrom($dto->unit) : $dto->unit);
        }

        if ($dto->context !== null) {
            $measurement->setContext(is_string($dto->context) ? GlucoseContext::tryFrom($dto->context) : $dto->context);
        }

        return $measurement;
    }

    public function mapEntityToResponse(BloodGlucoseMeasurement $measurement): BloodGlucoseMeasurementResponseDTO
    {
        return BloodGlucoseMeasurementResponseDTO::fromEntity($measurement);
    }
}
