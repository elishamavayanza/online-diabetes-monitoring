<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Healthcare\HealthcareOrganization;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prescriptions')]
class Prescription extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?HealthcareProfessional $prescriber = null;

    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?HealthcareOrganization $organization = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: 'string', length: 50, enumType: PrescriptionStatus::class)]
    private ?PrescriptionStatus $status = PrescriptionStatus::DRAFT;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $validatedAt = null;

    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?HealthcareProfessional $validatedBy = null;

    #[ORM\OneToMany(mappedBy: 'prescription', targetEntity: PrescriptionItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    #[ORM\OneToMany(mappedBy: 'prescription', targetEntity: PrescriptionVersion::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $versions;

    public function __construct()
    {
        parent::__construct();
        $this->items = new ArrayCollection();
        $this->versions = new ArrayCollection();
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

    public function getPrescriber(): ?HealthcareProfessional
    {
        return $this->prescriber;
    }

    public function setPrescriber(?HealthcareProfessional $prescriber): self
    {
        $this->prescriber = $prescriber;
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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getStatus(): ?PrescriptionStatus
    {
        return $this->status;
    }

    public function setStatus(PrescriptionStatus $status): self
    {
        $this->status = $status;
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

    public function getValidatedAt(): ?\DateTimeImmutable
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(?\DateTimeImmutable $validatedAt): self
    {
        $this->validatedAt = $validatedAt;
        return $this;
    }

    public function getValidatedBy(): ?HealthcareProfessional
    {
        return $this->validatedBy;
    }

    public function setValidatedBy(?HealthcareProfessional $validatedBy): self
    {
        $this->validatedBy = $validatedBy;
        return $this;
    }

    /**
     * @return Collection<int, PrescriptionItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(PrescriptionItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setPrescription($this);
        }
        return $this;
    }

    public function removeItem(PrescriptionItem $item): self
    {
        if ($this->items->removeElement($item)) {
            if ($item->getPrescription() === $this) {
                $item->setPrescription(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, PrescriptionVersion>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(PrescriptionVersion $version): self
    {
        if (!$this->versions->contains($version)) {
            $this->versions->add($version);
            $version->setPrescription($this);
        }
        return $this;
    }

    public function removeVersion(PrescriptionVersion $version): self
    {
        if ($this->versions->removeElement($version)) {
            if ($version->getPrescription() === $this) {
                $version->setPrescription(null);
            }
        }
        return $this;
    }
}
