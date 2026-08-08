<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\DiagnosisRequestDTO;
use App\DTO\Response\Medical\DiagnosisResponseDTO;
use App\Entity\Medical\Diagnosis;
use App\Entity\Medical\MedicalRecord;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;

class DiagnosisMapper
{
    public function mapRequestToEntity(
        DiagnosisRequestDTO $dto,
        Patient $patient,
        HealthcareProfessional $doctor,
        ?MedicalRecord $medicalRecord = null,
        ?Diagnosis $diagnosis = null
    ): Diagnosis {
        $diagnosis ??= new Diagnosis();

        $diagnosis->setPatient($patient);
        $diagnosis->setDoctor($doctor);
        $diagnosis->setConditionName($dto->conditionName);
        $diagnosis->setDescription($dto->description);
        $diagnosis->setDiagnosedAt($dto->diagnosedAt);
        $diagnosis->setStatus($dto->status);
        $diagnosis->setMedicalRecord($medicalRecord);

        return $diagnosis;
    }

    public function mapEntityToResponse(Diagnosis $diagnosis): DiagnosisResponseDTO
    {
        return DiagnosisResponseDTO::fromEntity($diagnosis);
    }
}
