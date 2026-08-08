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

        return $result;
    }

    public function mapEntityToResponse(LaboratoryResult $result): LaboratoryResultResponseDTO
    {
        return LaboratoryResultResponseDTO::fromEntity($result);
    }
}
