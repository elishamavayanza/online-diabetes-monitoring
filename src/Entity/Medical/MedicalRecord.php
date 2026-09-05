<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\Patient;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un dossier médical associé à une organisation de santé et à un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_medical_records')]
class MedicalRecord extends BaseEntity
{
    /**
     * @var Patient|null Le patient concerné par ce dossier médical.
     */
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(name: 'patient_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    /**
     * @var HealthcareOrganization|null L'organisation de santé gérant ou hébergeant le dossier.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareOrganization $organization = null;

    /**
     * @var MedicalRecordStatus|null Le statut actuel du dossier médical (ex: OPEN, CLOSED).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: MedicalRecordStatus::class)]
    private ?MedicalRecordStatus $status = MedicalRecordStatus::OPEN;

    /**
     * @var DateTimeImmutable|null La date et l'heure d'ouverture du dossier.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $openedAt = null;

    /**
     * @var DateTimeImmutable|null La date et l'heure de clôture du dossier (nullable si encore actif).
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $closedAt = null;

    /**
     * @var string|null Le motif de fermeture du dossier médical.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $closureReason = null;

    /**
     * Récupère le patient associé au dossier.
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * Définit le patient associé au dossier.
     */
    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * Récupère l'organisation de santé gérant le dossier.
     */
    public function getOrganization(): ?HealthcareOrganization
    {
        return $this->organization;
    }

    /**
     * Définit l'organisation de santé gérant le dossier.
     */
    public function setOrganization(?HealthcareOrganization $organization): static
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * Récupère le statut actuel du dossier.
     */
    public function getStatus(): ?MedicalRecordStatus
    {
        return $this->status;
    }

    /**
     * Définit le statut actuel du dossier.
     */
    public function setStatus(MedicalRecordStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Récupère la date et l'heure d'ouverture du dossier.
     */
    public function getOpenedAt(): ?DateTimeImmutable
    {
        return $this->openedAt;
    }

    /**
     * Définit la date et l'heure d'ouverture du dossier.
     */
    public function setOpenedAt(DateTimeImmutable $openedAt): static
    {
        $this->openedAt = $openedAt;
        return $this;
    }

    /**
     * Récupère la date et l'heure de clôture du dossier.
     */
    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    /**
     * Définit la date et l'heure de clôture du dossier.
     */
    public function setClosedAt(?DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;
        return $this;
    }

    /**
     * Récupère le motif de fermeture.
     */
    public function getClosureReason(): ?string
    {
        return $this->closureReason;
    }

    /**
     * Définit le motif de fermeture.
     */
    public function setClosureReason(?string $closureReason): static
    {
        $this->closureReason = $closureReason;
        return $this;
    }
}
