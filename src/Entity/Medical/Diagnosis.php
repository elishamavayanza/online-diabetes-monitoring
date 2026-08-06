<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'diagnoses')]
class Diagnosis extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareProfessional $doctor = null;

    #[ORM\Column(type: 'string', length: 150)]
    private ?string $conditionName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $diagnosedAt = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: MedicalRecord::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?MedicalRecord $medicalRecord = null;

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    public function getDoctor(): ?HealthcareProfessional
    {
        return $this->doctor;
    }

    public function setDoctor(?HealthcareProfessional $doctor): self
    {
        $this->doctor = $doctor;
        return $this;
    }

    public function getConditionName(): ?string
    {
        return $this->conditionName;
    }

    public function setConditionName(string $conditionName): self
    {
        $this->conditionName = $conditionName;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDiagnosedAt(): ?\DateTimeImmutable
    {
        return $this->diagnosedAt;
    }

    public function setDiagnosedAt(\DateTimeImmutable $diagnosedAt): self
    {
        $this->diagnosedAt = $diagnosedAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getMedicalRecord(): ?MedicalRecord
    {
        return $this->medicalRecord;
    }

    public function setMedicalRecord(?MedicalRecord $medicalRecord): self
    {
        $this->medicalRecord = $medicalRecord;
        return $this;
    }
}
