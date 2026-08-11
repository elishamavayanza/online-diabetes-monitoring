<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\PatientCommonOperation;
use App\Entity\Identity\Patient;
use App\Entity\Healthcare\HealthcareOrganization;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un dossier médical associé à une organisation de santé et à un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_medical_records')]
class MedicalRecord extends PatientCommonOperation
{
    /**
     * @var HealthcareOrganization|null L'organisation de santé gérant ou hébergeant le dossier.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareOrganization $organization = null;

    /**
     * @var MedicalRecordStatus|null Le statut actuel du dossier médical.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: MedicalRecordStatus::class)]
    private ?MedicalRecordStatus $status = MedicalRecordStatus::OPEN;

    /**
     * @var DateTimeImmutable|null La date et l'heure d'ouverture du dossier.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $openedAt = null;

    /**
     * @var DateTimeImmutable|null La date et l'heure de clôture du dossier.
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $closedAt = null;

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
     * Récupère le statut du dossier.
     */
    public function getStatus(): ?MedicalRecordStatus
    {
        return $this->status;
    }

    /**
     * Définit le statut du dossier.
     */
    public function setStatus(MedicalRecordStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Récupère la date d'ouverture.
     */
    public function getOpenedAt(): ?DateTimeImmutable
    {
        return $this->openedAt;
    }

    /**
     * Définit la date d'ouverture.
     */
    public function setOpenedAt(DateTimeImmutable $openedAt): static
    {
        $this->openedAt = $openedAt;
        return $this;
    }

    /**
     * Récupère la date de clôture.
     */
    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    /**
     * Définit la date de clôture.
     */
    public function setClosedAt(?DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;
        return $this;
    }
}
