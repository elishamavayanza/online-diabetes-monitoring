<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\LaboratoryResultRequestDTO;
use App\DTO\Response\Medical\LaboratoryResultResponseDTO;
use App\Entity\Medical\LaboratoryResult;
use App\Entity\Identity\Patient;

class LaboratoryResultMapper
{
    public function mapRequestToEntity(LaboratoryResultRequestDTO $dto, Patient $patient, ?LaboratoryResult $result = null): LaboratoryResult
    {
        $result ??= new LaboratoryResult();

        $result->setPatient($patient);
        $result->setTestName($dto->testName);
        $result->setFileUrl($dto->fileUrl);
        $result->setLabName($dto->labName);

        // Gestion de la date de mesure (measuredAt) pour éviter l'erreur SQL
        $measuredAt = property_exists($dto, 'measuredAt') && $dto->measuredAt
            ? new \DateTimeImmutable($dto->measuredAt)
            : new \DateTimeImmutable();

        if (method_exists($result, 'setMeasuredAt')) {
            $result->setMeasuredAt($measuredAt);
        }

        return $result;
    }

    public function mapEntityToResponse(LaboratoryResult $result): LaboratoryResultResponseDTO
    {
        return LaboratoryResultResponseDTO::fromEntity($result);
    }
}
