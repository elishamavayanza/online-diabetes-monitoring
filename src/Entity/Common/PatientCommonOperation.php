<?php

namespace App\Entity\Common;

use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class PatientCommonOperation extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(name: 'patient_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'issuer_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    protected ?User $issuer = null;

    #[ORM\Column(name: 'measured_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    protected ?DateTimeImmutable $measuredAt = null;

    #[ORM\Column(type: 'string', length: 50, enumType: MeasurementSource::class, nullable: true)]
    protected ?MeasurementSource $source = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $notes = null;

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    public function getIssuer(): ?User
    {
        return $this->issuer;
    }

    public function setIssuer(?User $issuer): static
    {
        $this->issuer = $issuer;
        return $this;
    }

    public function getMeasuredAt(): ?DateTimeImmutable
    {
        return $this->measuredAt;
    }

    public function setMeasuredAt(DateTimeImmutable $measuredAt): static
    {
        $this->measuredAt = $measuredAt;
        return $this;
    }

    public function getSource(): ?MeasurementSource
    {
        return $this->source;
    }

    public function setSource(?MeasurementSource $source): static
    {
        $this->source = $source;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }
}
