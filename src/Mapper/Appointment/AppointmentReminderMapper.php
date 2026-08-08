<?php

namespace App\Mapper\Appointment;

use App\DTO\Request\Appointment\AppointmentReminderRequestDTO;
use App\DTO\Response\Appointment\AppointmentReminderResponseDTO;
use App\Entity\Appointment\AppointmentReminder;
use App\Entity\Appointment\Appointment;
use App\Entity\Appointment\ReminderChannel;

class AppointmentReminderMapper
{
    public function mapRequestToEntity(
        AppointmentReminderRequestDTO $dto,
        Appointment $appointment,
        ?AppointmentReminder $reminder = null
    ): AppointmentReminder {
        $reminder ??= new AppointmentReminder();

        $reminder->setAppointment($appointment);

        if ($dto->channel !== null) {
            $reminder->setChannel(is_string($dto->channel) ? ReminderChannel::tryFrom($dto->channel) : $dto->channel);
        }

        $reminder->setScheduledFor($dto->scheduledFor);
        $reminder->setSentAt($dto->sentAt);

        return $reminder;
    }

    public function mapEntityToResponse(AppointmentReminder $reminder): AppointmentReminderResponseDTO
    {
        return AppointmentReminderResponseDTO::fromEntity($reminder);
    }
}
