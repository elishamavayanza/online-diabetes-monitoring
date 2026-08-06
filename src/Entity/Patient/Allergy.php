<?php

namespace App\Entity\Patient;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'allergies')]
class Allergy extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 50, enumType: AllergySeverity::class)]
    private ?AllergySeverity $severity = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reaction = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $diagnosedAt = null;

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSeverity(): ?AllergySeverity
    {
        return $this->severity;
    }

    public function setSeverity(AllergySeverity $severity): self
    {
        $this->severity = $severity;
        return $this;
    }

    public function getReaction(): ?string
    {
        return $this->reaction;
    }

    public function setReaction(?string $reaction): self
    {
        $this->reaction = $reaction;
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

    public function getDiagnosedAt(): ?\DateTimeImmutable
    {
        return $this->diagnosedAt;
    }

    public function setDiagnosedAt(\DateTimeImmutable $diagnosedAt): self
    {
        $this->diagnosedAt = $diagnosedAt;
        return $this;
    }
}
