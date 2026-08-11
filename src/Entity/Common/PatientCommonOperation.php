<?php

namespace App\Entity\Common;

use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Super-classe abstraite (MappedSuperclass) regroupant toutes les propriétés et comportements
 * communs aux opérations, mesures et documents associés à un patient dans le système.
 */
#[ORM\MappedSuperclass]
#[ORM\Table(name: 'common_patient_file_attachments')]
abstract class PatientCommonOperation extends BaseEntity
{
    /**
     * @var Patient|null Le patient concerné par cette opération ou mesure.
     */
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(name: 'patient_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected ?Patient $patient = null;

    /**
     * @var User|null L'utilisateur (professionnel, admin, etc.) qui a émis ou enregistré l'opération.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'issuer_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    protected ?User $issuer = null;

    /**
     * @var DateTimeImmutable|null La date et l'heure exactes où la mesure ou l'opération a eu lieu.
     */
    #[ORM\Column(name: 'measured_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    protected ?DateTimeImmutable $measuredAt = null;

    /**
     * @var MeasurementSource|null La source ou l'origine de la mesure (ex: manuelle, appareil connecté, etc.).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: MeasurementSource::class, nullable: true)]
    protected ?MeasurementSource $source = null;

    /**
     * @var string|null Notes ou observations textuelles complémentaires concernant l'opération.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $notes = null;

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
     * Récupère l'émetteur de l'opération.
     */
    public function getIssuer(): ?User
    {
        return $this->issuer;
    }

    /**
     * Définit l'émetteur de l'opération.
     */
    public function setIssuer(?User $issuer): static
    {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * Récupère la date et l'heure de la mesure.
     */
    public function getMeasuredAt(): ?DateTimeImmutable
    {
        return $this->measuredAt;
    }

    /**
     * Définit la date et l'heure de la mesure.
     */
    public function setMeasuredAt(DateTimeImmutable $measuredAt): static
    {
        $this->measuredAt = $measuredAt;
        return $this;
    }

    /**
     * Récupère la source de la mesure.
     */
    public function getSource(): ?MeasurementSource
    {
        return $this->source;
    }

    /**
     * Définit la source de la mesure.
     */
    public function setSource(?MeasurementSource $source): static
    {
        $this->source = $source;
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
}
