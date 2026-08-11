<?php

namespace App\Entity\Patient;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une allergie diagnostiquée chez un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'patient_allergies')]
class Allergy extends BaseEntity
{
    /**
     * @var Patient|null Le patient concerné par l'allergie.
     */
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    /**
     * @var string|null Le nom ou l'allergène (ex: Pénicilline, Arachides).
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    /**
     * @var AllergySeverity|null Le niveau de sévérité de l'allergie.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: AllergySeverity::class)]
    private ?AllergySeverity $severity = null;

    /**
     * @var string|null La réaction allergique observée ou décrite.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reaction = null;

    /**
     * @var string|null Notes ou informations complémentaires.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    /**
     * @var \DateTimeImmutable|null La date du diagnostic de l'allergie.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $diagnosedAt = null;

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
     * Récupère le nom de l'allergie.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de l'allergie.
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Récupère la sévérité de l'allergie.
     */
    public function getSeverity(): ?AllergySeverity
    {
        return $this->severity;
    }

    /**
     * Définit la sévérité de l'allergie.
     */
    public function setSeverity(AllergySeverity $severity): static
    {
        $this->severity = $severity;
        return $this;
    }

    /**
     * Récupère la réaction allergique.
     */
    public function getReaction(): ?string
    {
        return $this->reaction;
    }

    /**
     * Définit la réaction allergique.
     */
    public function setReaction(?string $reaction): static
    {
        $this->reaction = $reaction;
        return $this;
    }

    /**
     * Récupère les notes.
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * Définit les notes.
     */
    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
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
}
