<?php

namespace App\Mapper\Audit;

use App\DTO\Request\Audit\DataAccessLogRequestDTO;
use App\DTO\Response\Audit\DataAccessLogResponseDTO;
use App\Entity\Audit\DataAccessLog;
use App\Entity\Identity\User;
use App\Entity\Identity\Patient;

class DataAccessLogMapper
{
    public function mapRequestToEntity(
        DataAccessLogRequestDTO $dto,
        User $accessedBy,
        Patient $patient,
        ?DataAccessLog $log = null
    ): DataAccessLog {
        $log ??= new DataAccessLog();

        $log->setAccessedBy($accessedBy);
        $log->setPatient($patient);
        $log->setResourceType($dto->resourceType);
        $log->setResourceId($dto->resourceId);
        $log->setReason($dto->reason);
        $log->setAccessedAt($dto->accessedAt);

        return $log;
    }

    public function mapEntityToResponse(DataAccessLog $log): DataAccessLogResponseDTO
    {
        return DataAccessLogResponseDTO::fromEntity($log);
    }
}
