<?php

namespace App\Entity\Common;

use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class PatientMeasurement extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected ?Patient $patient = null;

    #[ORM\Column(type: 'datetime_immutable')]
    protected ?\DateTimeImmutable $measuredAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected ?User $recordedBy = null;

    #[ORM\Column(type: 'string', length: 50, enumType: MeasurementSource::class)]
    protected ?MeasurementSource $source = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $notes = null;

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    public function getMeasuredAt(): ?\DateTimeImmutable
    {
        return $this->measuredAt;
    }

    public function setMeasuredAt(\DateTimeImmutable $measuredAt): self
    {
        $this->measuredAt = $measuredAt;
        return $this;
    }

    public function getRecordedBy(): ?User
    {
        return $this->recordedBy;
    }

    public function setRecordedBy(?User $recordedBy): self
    {
        $this->recordedBy = $recordedBy;
        return $this;
    }

    public function getSource(): ?MeasurementSource
    {
        return $this->source;
    }

    public function setSource(MeasurementSource $source): self
    {
        $this->source = $source;
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
}
