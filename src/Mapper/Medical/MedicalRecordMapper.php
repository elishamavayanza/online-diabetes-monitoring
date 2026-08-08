<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\MedicalRecordRequestDTO;
use App\DTO\Response\Medical\MedicalRecordResponseDTO;
use App\Entity\Medical\MedicalRecord;
use App\Entity\Medical\MedicalRecordStatus;
use App\Entity\Identity\Patient;
use App\Entity\Healthcare\HealthcareOrganization;

class MedicalRecordMapper
{
    public function mapRequestToEntity(
        MedicalRecordRequestDTO $dto,
        Patient $patient,
        HealthcareOrganization $organization,
        ?MedicalRecord $record = null
    ): MedicalRecord {
        $record ??= new MedicalRecord();

        $record->setPatient($patient);
        $record->setOrganization($organization);

        if ($dto->status !== null) {
            $record->setStatus(is_string($dto->status) ? MedicalRecordStatus::tryFrom($dto->status) : $dto->status);
        }

        $record->setOpenedAt($dto->openedAt);
        $record->setClosedAt($dto->closedAt);

        return $record;
    }

    public function mapEntityToResponse(MedicalRecord $record): MedicalRecordResponseDTO
    {
        return MedicalRecordResponseDTO::fromEntity($record);
    }
}
