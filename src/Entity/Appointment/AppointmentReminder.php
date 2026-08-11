<?php

namespace App\Entity\Appointment;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un rappel automatique ou planifié envoyé à un patient
 * pour lui notifier un rendez-vous médical à venir.
 */
#[ORM\Entity]
#[ORM\Table(name: 'appointment_appointment_reminders')]
class AppointmentReminder extends BaseEntity
{
    /**
     * @var Appointment|null Le rendez-vous associé à ce rappel.
     */
    #[ORM\ManyToOne(targetEntity: Appointment::class, inversedBy: 'reminders')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Appointment $appointment = null;

    /**
     * @var ReminderChannel|null Le canal d'envoi du rappel (ex: SMS, EMAIL, PUSH_NOTIFICATION).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: ReminderChannel::class)]
    private ?ReminderChannel $channel = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure prévues pour l'envoi du rappel.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $scheduledFor = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure réelles d'envoi du rappel (null si pas encore envoyé).
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $sentAt = null;

    /**
     * Récupère le rendez-vous médical associé.
     */
    public function getAppointment(): ?Appointment
    {
        return $this->appointment;
    }

    /**
     * Définit le rendez-vous médical associé.
     */
    public function setAppointment(?Appointment $appointment): static
    {
        $this->appointment = $appointment;
        return $this;
    }

    /**
     * Récupère le canal de notification.
     */
    public function getChannel(): ?ReminderChannel
    {
        return $this->channel;
    }

    /**
     * Définit le canal de notification.
     */
    public function setChannel(ReminderChannel $channel): static
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * Récupère la date d'envoi programmée du rappel.
     */
    public function getScheduledFor(): ?\DateTimeImmutable
    {
        return $this->scheduledFor;
    }

    /**
     * Définit la date d'envoi programmée du rappel.
     */
    public function setScheduledFor(\DateTimeImmutable $scheduledFor): static
    {
        $this->scheduledFor = $scheduledFor;
        return $this;
    }

    /**
     * Récupère la date et l'heure effectives d'envoi.
     */
    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    /**
     * Définit la date et l'heure effectives d'envoi.
     */
    public function setSentAt(?\DateTimeImmutable $sentAt): static
    {
        $this->sentAt = $sentAt;
        return $this;
    }
}
