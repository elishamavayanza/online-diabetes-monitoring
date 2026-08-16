<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\BloodGlucoseMeasurementRequestDTO;
use App\DTO\Response\Medical\BloodGlucoseMeasurementResponseDTO;
use App\Entity\Medical\BloodGlucoseMeasurement;
use App\Entity\Medical\GlucoseContext;
use App\Entity\Medical\GlucoseUnit;
use App\Entity\Identity\Patient;
use Symfony\Bundle\SecurityBundle\Security;
class BloodGlucoseMeasurementMapper
{
    // 2. Injectez le composant Security de Symfony via le constructeur
    public function __construct(
        private readonly Security $security
    ) {}

    public function mapRequestToEntity(BloodGlucoseMeasurementRequestDTO $dto, Patient $patient, ?BloodGlucoseMeasurement $measurement = null): BloodGlucoseMeasurement
    {
        $isNew = $measurement === null;
        $measurement ??= new BloodGlucoseMeasurement();

        $measurement->setPatient($patient);
        $measurement->setValue($dto->value);

        if ($dto->unit !== null) {
            $measurement->setUnit(is_string($dto->unit) ? GlucoseUnit::tryFrom($dto->unit) : $dto->unit);
        }

        if ($dto->context !== null) {
            $measurement->setContext(is_string($dto->context) ? GlucoseContext::tryFrom($dto->context) : $dto->context);
        }

        // 3. Définissez l'émetteur (uniquement à la création)
        if ($isNew) {
            $user = $this->security->getUser();
            if ($user) {
                $measurement->setIssuer($user);
            }
        }

        // 4. Gérez la date de la mesure (measuredAt)
        // Si le DTO possède une date, utilisez-la, sinon prenez la date/heure actuelle
        if (property_exists($dto, 'measuredAt') && $dto->measuredAt !== null) {
            $measurement->setMeasuredAt(
                is_string($dto->measuredAt) ? new \DateTimeImmutable($dto->measuredAt) : $dto->measuredAt
            );
        } elseif ($isNew && $measurement->getMeasuredAt() === null) {
            $measurement->setMeasuredAt(new \DateTimeImmutable());
        }

        return $measurement;
    }

    public function mapEntityToResponse(BloodGlucoseMeasurement $measurement): BloodGlucoseMeasurementResponseDTO
    {
        return BloodGlucoseMeasurementResponseDTO::fromEntity($measurement);
    }
}
