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

#[ORM\Entity]
#[ORM\Table(name: 'appointments')]
class Appointment extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareProfessional $professional = null;

    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareOrganization $organization = null;

    #[ORM\ManyToOne(targetEntity: HealthcareFacility::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?HealthcareFacility $facility = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $scheduledAt = null;

    #[ORM\Column(type: 'integer')]
    private ?int $durationMinutes = null;

    #[ORM\Column(type: 'string', length: 50, enumType: AppointmentStatus::class)]
    private ?AppointmentStatus $status = AppointmentStatus::SCHEDULED;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\OneToMany(mappedBy: 'appointment', targetEntity: AppointmentReminder::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $reminders;

    public function __construct()
    {
        parent::__construct();
        $this->reminders = new ArrayCollection();
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    public function getProfessional(): ?HealthcareProfessional
    {
        return $this->professional;
    }

    public function setProfessional(?HealthcareProfessional $professional): self
    {
        $this->professional = $professional;
        return $this;
    }

    public function getOrganization(): ?HealthcareOrganization
    {
        return $this->organization;
    }

    public function setOrganization(?HealthcareOrganization $organization): self
    {
        $this->organization = $organization;
        return $this;
    }

    public function getFacility(): ?HealthcareFacility
    {
        return $this->facility;
    }

    public function setFacility(?HealthcareFacility $facility): self
    {
        $this->facility = $facility;
        return $this;
    }

    public function getScheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(\DateTimeImmutable $scheduledAt): self
    {
        $this->scheduledAt = $scheduledAt;
        return $this;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): self
    {
        $this->durationMinutes = $durationMinutes;
        return $this;
    }

    public function getStatus(): ?AppointmentStatus
    {
        return $this->status;
    }

    public function setStatus(AppointmentStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return Collection<int, AppointmentReminder>
     */
    public function getReminders(): Collection
    {
        return $this->reminders;
    }

    public function addReminder(AppointmentReminder $reminder): self
    {
        if (!$this->reminders->contains($reminder)) {
            $this->reminders->add($reminder);
            $reminder->setAppointment($this);
        }
        return $this;
    }

    public function removeReminder(AppointmentReminder $reminder): self
    {
        if ($this->reminders->removeElement($reminder)) {
            if ($reminder->getAppointment() === $this) {
                $reminder->setAppointment(null);
            }
        }
        return $this;
    }
}
