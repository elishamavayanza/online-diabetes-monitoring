<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\BloodPressureMeasurementRequestDTO;
use App\DTO\Response\Medical\BloodPressureMeasurementResponseDTO;
use App\Entity\Medical\BloodPressureMeasurement;
use App\Entity\Identity\Patient;
use Symfony\Bundle\SecurityBundle\Security; // Importez Security

class BloodPressureMeasurementMapper
{
    // Injection du service Security pour récupérer l'utilisateur connecté
    public function __construct(
        private readonly Security $security
    ) {}

    public function mapRequestToEntity(BloodPressureMeasurementRequestDTO $dto, Patient $patient, ?BloodPressureMeasurement $measurement = null): BloodPressureMeasurement
    {
        $isNew = $measurement === null;
        $measurement ??= new BloodPressureMeasurement();

        $measurement->setPatient($patient);
        $measurement->setSystolic($dto->systolic);
        $measurement->setDiastolic($dto->diastolic);
        $measurement->setPulse($dto->pulse);

        // 1. Définir l'émetteur (uniquement à la création)
        if ($isNew) {
            $user = $this->security->getUser();
            if ($user) {
                $measurement->setIssuer($user);
            }
        }

        // 2. Initialiser la date de mesure (measuredAt)
        // S'il n'est pas déjà défini, on utilise la date actuelle
        if ($measurement->getMeasuredAt() === null) {
            $measurement->setMeasuredAt(new \DateTimeImmutable());
        }

        return $measurement;
    }

    public function mapEntityToResponse(BloodPressureMeasurement $measurement): BloodPressureMeasurementResponseDTO
    {
        return BloodPressureMeasurementResponseDTO::fromEntity($measurement);
    }
}
