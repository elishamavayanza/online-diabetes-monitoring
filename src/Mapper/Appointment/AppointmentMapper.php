<?php

namespace App\Mapper\Appointment;

use App\DTO\Request\Appointment\AppointmentRequestDTO;
use App\DTO\Response\Appointment\AppointmentResponseDTO;
use App\Entity\Appointment\Appointment;
use App\Entity\Appointment\AppointmentStatus;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Healthcare\Facility;

class AppointmentMapper
{
    public function mapRequestToEntity(
        AppointmentRequestDTO $dto,
        Patient $patient,
        HealthcareProfessional $professional,
        HealthcareOrganization $organization,
        ?Facility $facility = null,
        ?Appointment $appointment = null
    ): Appointment {
        $appointment ??= new Appointment();

        $appointment->setPatient($patient);
        $appointment->setProfessional($professional);
        $appointment->setOrganization($organization);
        $appointment->setFacility($facility);
        $appointment->setScheduledAt($dto->scheduledAt);
        $appointment->setDurationMinutes($dto->durationMinutes);

        if ($dto->status !== null) {
            $appointment->setStatus(is_string($dto->status) ? AppointmentStatus::tryFrom($dto->status) : $dto->status);
        }

        $appointment->setReason($dto->reason);
        $appointment->setNotes($dto->notes);

        return $appointment;
    }

    public function mapEntityToResponse(Appointment $appointment): AppointmentResponseDTO
    {
        return AppointmentResponseDTO::fromEntity($appointment);
    }
}
