<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\WeightMeasurementRequestDTO;
use App\DTO\Response\Medical\WeightMeasurementResponseDTO;
use App\Entity\Medical\WeightMeasurement;
use App\Entity\Identity\Patient;
use App\Security\SecurityServiceInterface; // Importez l'interface

class WeightMeasurementMapper
{
    // Injectez le service de sécurité ici
    public function __construct(
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function mapRequestToEntity(WeightMeasurementRequestDTO $dto, Patient $patient, ?WeightMeasurement $measurement = null): WeightMeasurement
    {
        $measurement ??= new WeightMeasurement();

        $measurement->setPatient($patient);
        $measurement->setValueKg($dto->valueKg);
        $measurement->setHeightCm($dto->heightCm);

        // Initialisation des champs obligatoires
        if ($measurement->getMeasuredAt() === null) {
            $measurement->setMeasuredAt(new \DateTimeImmutable());
        }

        // Récupération et assignation de l'utilisateur courant (l'auteur de la mesure)
        if ($measurement->getIssuer() === null) {
            $currentUser = $this->securityService->getCurrentUser();
            $measurement->setIssuer($currentUser);
        }

        return $measurement;
    }

    public function mapEntityToResponse(WeightMeasurement $measurement): WeightMeasurementResponseDTO
    {
        return WeightMeasurementResponseDTO::fromEntity($measurement);
    }
}
