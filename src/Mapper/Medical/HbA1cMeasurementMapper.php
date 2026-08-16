<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\HbA1cMeasurementRequestDTO;
use App\DTO\Response\Medical\HbA1cMeasurementResponseDTO;
use App\Entity\Medical\HbA1cMeasurement;
use App\Entity\Identity\Patient;
use App\Security\SecurityServiceInterface;

class HbA1cMeasurementMapper
{
    public function __construct(
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function mapRequestToEntity(HbA1cMeasurementRequestDTO $dto, Patient $patient, ?HbA1cMeasurement $measurement = null): HbA1cMeasurement
    {
        $measurement ??= new HbA1cMeasurement();

        $measurement->setPatient($patient);
        $measurement->setValuePercent($dto->valuePercent);

        // Gestion de la date de mesure
        $measuredAt = $dto->measuredAt ? new \DateTimeImmutable($dto->measuredAt) : new \DateTimeImmutable();
        if (method_exists($measurement, 'setMeasuredAt')) {
            $measurement->setMeasuredAt($measuredAt);
        }

        // Association de l'utilisateur connecté en tant qu'émetteur (issuer)
        $currentUser = $this->securityService->getCurrentUser();
        if (method_exists($measurement, 'setIssuer')) {
            $measurement->setIssuer($currentUser);
        }

        return $measurement;
    }

    public function mapEntityToResponse(HbA1cMeasurement $measurement): HbA1cMeasurementResponseDTO
    {
        return HbA1cMeasurementResponseDTO::fromEntity($measurement);
    }
}
