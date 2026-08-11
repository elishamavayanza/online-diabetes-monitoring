<?php

namespace App\Entity\Appointment;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Healthcare\HealthcareFacility;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un rendez-vous médical planifié entre un patient et un professionnel de santé,
 * au sein d'une organisation et éventuellement d'une infrastructure spécifique.
 */
#[ORM\Entity]
#[ORM\Table(name: 'appointment_appointments')]
class Appointment extends BaseEntity
{
    /**
     * @var Patient|null Le patient concerné par le rendez-vous.
     */
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    /**
     * @var HealthcareProfessional|null Le professionnel de santé qui assure la consultation.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareProfessional $professional = null;

    /**
     * @var HealthcareOrganization|null L'organisation de santé (hôpital, réseau, etc.) hébergeant le rendez-vous.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareOrganization $organization = null;

    /**
     * @var HealthcareFacility|null Le site physique ou bâtiment spécifique (facultatif).
     */
    #[ORM\ManyToOne(targetEntity: HealthcareFacility::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?HealthcareFacility $facility = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure prévues du rendez-vous.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $scheduledAt = null;

    /**
     * @var int|null La durée prévue du rendez-vous en minutes.
     */
    #[ORM\Column(type: 'integer')]
    private ?int $durationMinutes = null;

    /**
     * @var AppointmentStatus|null Le statut actuel du rendez-vous (ex: planifié, annulé, terminé).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: AppointmentStatus::class)]
    private ?AppointmentStatus $status = AppointmentStatus::SCHEDULED;

    /**
     * @var string|null Le motif principal de la consultation.
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $reason = null;

    /**
     * @var string|null Notes additionnelles ou instructions concernant le rendez-vous.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    /**
     * @var Collection<int, AppointmentReminder> La liste des rappels programmés pour ce rendez-vous.
     */
    #[ORM\OneToMany(mappedBy: 'appointment', targetEntity: AppointmentReminder::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $reminders;

    /**
     * Initialise la collection des rappels de rendez-vous.
     */
    public function __construct()
    {
        $this->reminders = new ArrayCollection();
    }

    /**
     * Récupère le patient associé au rendez-vous.
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * Définit le patient associé au rendez-vous.
     */
    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * Récupère le professionnel de santé.
     */
    public function getProfessional(): ?HealthcareProfessional
    {
        return $this->professional;
    }

    /**
     * Définit le professionnel de santé.
     */
    public function setProfessional(?HealthcareProfessional $professional): static
    {
        $this->professional = $professional;
        return $this;
    }

    /**
     * Récupère l'organisation de santé.
     */
    public function getOrganization(): ?HealthcareOrganization
    {
        return $this->organization;
    }

    /**
     * Définit l'organisation de santé.
     */
    public function setOrganization(?HealthcareOrganization $organization): static
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * Récupère l'infrastructure/bâtiment spécifique.
     */
    public function getFacility(): ?HealthcareFacility
    {
        return $this->facility;
    }

    /**
     * Définit l'infrastructure/bâtiment spécifique.
     */
    public function setFacility(?HealthcareFacility $facility): static
    {
        $this->facility = $facility;
        return $this;
    }

    /**
     * Récupère la date et l'heure prévues.
     */
    public function getScheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    /**
     * Définit la date et l'heure prévues.
     */
    public function setScheduledAt(\DateTimeImmutable $scheduledAt): static
    {
        $this->scheduledAt = $scheduledAt;
        return $this;
    }

    /**
     * Récupère la durée du rendez-vous en minutes.
     */
    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    /**
     * Définit la durée du rendez-vous en minutes.
     */
    public function setDurationMinutes(int $durationMinutes): static
    {
        $this->durationMinutes = $durationMinutes;
        return $this;
    }

    /**
     * Récupère le statut actuel du rendez-vous.
     */
    public function getStatus(): ?AppointmentStatus
    {
        return $this->status;
    }

    /**
     * Modifie le statut du rendez-vous.
     */
    public function setStatus(AppointmentStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Récupère le motif du rendez-vous.
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * Définit le motif du rendez-vous.
     */
    public function setReason(?string $reason): static
    {
        $this->reason = $reason;
        return $this;
    }

    /**
     * Récupère les notes complémentaires.
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * Définit les notes complémentaires.
     */
    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * Retourne la collection des rappels associés.
     *
     * @return Collection<int, AppointmentReminder>
     */
    public function getReminders(): Collection
    {
        return $this->reminders;
    }

    /**
     * Ajoute un rappel au rendez-vous.
     */
    public function addReminder(AppointmentReminder $reminder): static
    {
        if (!$this->reminders->contains($reminder)) {
            $this->reminders->add($reminder);
            $reminder->setAppointment($this);
        }
        return $this;
    }

    /**
     * Retire un rappel du rendez-vous.
     */
    public function removeReminder(AppointmentReminder $reminder): static
    {
        if ($this->reminders->removeElement($reminder)) {
            if ($reminder->getAppointment() === $this) {
                $reminder->setAppointment(null);
            }
        }
        return $this;
    }
}
