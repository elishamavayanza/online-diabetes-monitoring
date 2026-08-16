<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\DiagnosisRequestDTO;
use App\DTO\Response\Medical\DiagnosisResponseDTO;
use App\Entity\Medical\Diagnosis;
use App\Entity\Medical\MedicalRecord;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use Symfony\Bundle\SecurityBundle\Security; // Importez Security

class DiagnosisMapper
{
    public function __construct(
        private readonly Security $security
    ) {}

    public function mapRequestToEntity(
        DiagnosisRequestDTO $dto,
        Patient $patient,
        HealthcareProfessional $doctor,
        ?MedicalRecord $medicalRecord = null,
        ?Diagnosis $diagnosis = null
    ): Diagnosis {
        $isNew = $diagnosis === null;
        $diagnosis ??= new Diagnosis();

        $diagnosis->setPatient($patient);
        $diagnosis->setDoctor($doctor);
        $diagnosis->setConditionName($dto->conditionName);
        $diagnosis->setDescription($dto->description);
        $diagnosis->setDiagnosedAt($dto->diagnosedAt);
        $diagnosis->setStatus($dto->status);
        $diagnosis->setMedicalRecord($medicalRecord);

        // 1. Correction pour le champ 'measured_at' hérité qui pose problème
        if (method_exists($diagnosis, 'setMeasuredAt') && $diagnosis->getMeasuredAt() === null) {
            // On utilise la date du diagnostic ou la date du jour par défaut
            $diagnosis->setMeasuredAt($dto->diagnosedAt ?? new \DateTimeImmutable());
        }

        // 2. Définir l'émetteur (issuer) si l'entité en hérite et que c'est une création
        if ($isNew && method_exists($diagnosis, 'setIssuer')) {
            $user = $this->security->getUser();
            if ($user) {
                $diagnosis->setIssuer($user);
            }
        }

        return $diagnosis;
    }

    public function mapEntityToResponse(Diagnosis $diagnosis): DiagnosisResponseDTO
    {
        return DiagnosisResponseDTO::fromEntity($diagnosis);
    }
}
