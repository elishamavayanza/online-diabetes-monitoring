<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\PatientCommonOperation;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un diagnostic médical établi par un professionnel de santé pour un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_diagnoses')]
class Diagnosis extends PatientCommonOperation
{
    /**
     * @var HealthcareProfessional|null Le médecin ou professionnel de santé ayant établi le diagnostic.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareProfessional $doctor = null;

    /**
     * @var string|null Le nom ou l'intitulé de la condition/pathologie diagnostiquée.
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $conditionName = null;

    /**
     * @var string|null La description détaillée du diagnostic.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure à laquelle le diagnostic a été posé.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $diagnosedAt = null;

    /**
     * @var string|null Le statut actuel du diagnostic (ex: actif, résolu, chronique).
     */
    #[ORM\Column(type: 'string', length: 50)]
    private ?string $status = null;

    /**
     * @var MedicalRecord|null Le dossier médical associé au diagnostic.
     */
    #[ORM\ManyToOne(targetEntity: MedicalRecord::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?MedicalRecord $medicalRecord = null;

    /**
     * Récupère le patient associé.
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * Définit le patient associé.
     */
    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * Récupère le médecin associé.
     */
    public function getDoctor(): ?HealthcareProfessional
    {
        return $this->doctor;
    }

    /**
     * Définit le médecin associé.
     */
    public function setDoctor(?HealthcareProfessional $doctor): static
    {
        $this->doctor = $doctor;
        return $this;
    }

    /**
     * Récupère le nom de la condition.
     */
    public function getConditionName(): ?string
    {
        return $this->conditionName;
    }

    /**
     * Définit le nom de la condition.
     */
    public function setConditionName(string $conditionName): static
    {
        $this->conditionName = $conditionName;
        return $this;
    }

    /**
     * Récupère la description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description.
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Récupère la date du diagnostic.
     */
    public function getDiagnosedAt(): ?\DateTimeImmutable
    {
        return $this->diagnosedAt;
    }

    /**
     * Définit la date du diagnostic.
     */
    public function setDiagnosedAt(\DateTimeImmutable $diagnosedAt): static
    {
        $this->diagnosedAt = $diagnosedAt;
        return $this;
    }

    /**
     * Récupère le statut du diagnostic.
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Définit le statut du diagnostic.
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Récupère le dossier médical associé.
     */
    public function getMedicalRecord(): ?MedicalRecord
    {
        return $this->medicalRecord;
    }

    /**
     * Définit le dossier médical associé.
     */
    public function setMedicalRecord(?MedicalRecord $medicalRecord): static
    {
        $this->medicalRecord = $medicalRecord;
        return $this;
    }
}
