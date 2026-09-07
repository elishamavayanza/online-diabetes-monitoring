<?php

namespace App\Mapper\Treatment;

use App\DTO\Request\Treatment\InsulinInjectionRequestDTO;
use App\DTO\Response\Treatment\InsulinInjectionResponseDTO;
use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use App\Entity\Treatment\InjectionSite;
use App\Entity\Treatment\Insulin;
use App\Entity\Treatment\InsulinInjection;
use App\Entity\Treatment\IntakeStatus;
use App\Entity\Treatment\PrescriptionItem;

class InsulinInjectionMapper
{
    public function mapRequestToEntity(InsulinInjectionRequestDTO $dto, PrescriptionItem $prescriptionItem, Insulin $insulin, Patient $patient, User $issuer, ?InsulinInjection $injection = null): InsulinInjection
    {
        $injection ??= new InsulinInjection();

        $injection->setPatient($patient);
        $injection->setPrescriptionItem($prescriptionItem);
        $injection->setInsulin($insulin);
        $injection->setInjectedAt($dto->injectedAt);
        $injection->setDoseUnits($dto->doseUnits);

        if ($dto->injectionSite !== null) {
            $injection->setInjectionSite(is_string($dto->injectionSite) ? InjectionSite::tryFrom($dto->injectionSite) : $dto->injectionSite);
        }

        if ($dto->status !== null) {
            $injection->setStatus(is_string($dto->status) ? IntakeStatus::tryFrom($dto->status) : $dto->status);
        }

        $injection->setIssuer($issuer);
        $injection->setNotes($dto->notes);

        return $injection;
    }

    public function mapEntityToResponse(InsulinInjection $injection): InsulinInjectionResponseDTO
    {
        return InsulinInjectionResponseDTO::fromEntity($injection);
    }
}