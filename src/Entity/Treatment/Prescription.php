<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Healthcare\HealthcareOrganization;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une prescription médicale émise pour un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'treatment_prescriptions')]
class Prescription extends BaseEntity
{
    /**
     * @var Patient|null Le patient bénéficiant de la prescription.
     */
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    /**
     * @var HealthcareProfessional|null Le professionnel de santé prescripteur.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?HealthcareProfessional $prescriber = null;

    /**
     * @var HealthcareOrganization|null L'organisation de santé rattachée à la prescription.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?HealthcareOrganization $organization = null;

    /**
     * @var \DateTimeImmutable|null La date de début de la prescription.
     */
    #[ORM\Column(type: 'date_immutable')]
    private ?\DateTimeImmutable $startDate = null;

    /**
     * @var \DateTimeImmutable|null La date de fin de la prescription.
     */
    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    /**
     * @var string|null Le statut actuel de la prescription.
     */
    #[ORM\Column(type: 'string', length: 50)]
    private ?string $status = null;

    /**
     * @var string|null Notes ou instructions particulières concernant la prescription.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure de validation de la prescription.
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $validatedAt = null;

    /**
     * @var HealthcareProfessional|null Le professionnel de santé ayant validé la prescription.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?HealthcareProfessional $validatedBy = null;

    /**
     * @var Collection<int, PrescriptionItem> La collection des éléments composant la prescription.
     */
    #[ORM\OneToMany(targetEntity: PrescriptionItem::class, mappedBy: 'prescription', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    /**
     * @var Collection<int, PrescriptionVersion> L'historique des versions de la prescription.
     */
    #[ORM\OneToMany(targetEntity: PrescriptionVersion::class, mappedBy: 'prescription', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $versions;

    /**
     * Constructeur pour initialiser les collections.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->versions = new ArrayCollection();
        $this->status ??= PrescriptionStatus::DRAFT->value;
    }

    /**
     * Récupère le patient.
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * Définit le patient.
     */
    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * Récupère le prescripteur.
     */
    public function getPrescriber(): ?HealthcareProfessional
    {
        return $this->prescriber;
    }

    /**
     * Définit le prescripteur.
     */
    public function setPrescriber(?HealthcareProfessional $prescriber): static
    {
        $this->prescriber = $prescriber;
        return $this;
    }

    /**
     * Récupère l'organisation.
     */
    public function getOrganization(): ?HealthcareOrganization
    {
        return $this->organization;
    }

    /**
     * Définit l'organisation.
     */
    public function setOrganization(?HealthcareOrganization $organization): static
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * Récupère la date de début.
     */
    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    /**
     * Définit la date de début.
     */
    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Récupère la date de fin.
     */
    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * Définit la date de fin.
     */
    public function setEndDate(?\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Récupère le statut sous forme d'énumération.
     */
    public function getStatus(): ?PrescriptionStatus
    {
        return $this->status ? PrescriptionStatus::tryFrom($this->status) : null;
    }

    /**
     * Définit le statut.
     */
    public function setStatus(PrescriptionStatus|string $status): static
    {
        $this->status = $status instanceof PrescriptionStatus ? $status->value : $status;
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
     * Récupère la date de validation.
     */
    public function getValidatedAt(): ?\DateTimeImmutable
    {
        return $this->validatedAt;
    }

    /**
     * Définit la date de validation.
     */
    public function setValidatedAt(?\DateTimeImmutable $validatedAt): static
    {
        $this->validatedAt = $validatedAt;
        return $this;
    }

    /**
     * Récupère le valideur.
     */
    public function getValidatedBy(): ?HealthcareProfessional
    {
        return $this->validatedBy;
    }

    /**
     * Définit le valideur.
     */
    public function setValidatedBy(?HealthcareProfessional $validatedBy): static
    {
        $this->validatedBy = $validatedBy;
        return $this;
    }

    /**
     * Récupère les éléments de la prescription.
     *
     * @return Collection<int, PrescriptionItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Ajoute un élément à la prescription.
     */
    public function addItem(PrescriptionItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setPrescription($this);
        }
        return $this;
    }

    /**
     * Retire un élément de la prescription.
     */
    public function removeItem(PrescriptionItem $item): static
    {
        if ($this->items->removeElement($item)) {
            if ($item->getPrescription() === $this) {
                $item->setPrescription(null);
            }
        }
        return $this;
    }

    /**
     * Récupère les versions de la prescription.
     *
     * @return Collection<int, PrescriptionVersion>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    /**
     * Ajoute une version à l'historique.
     */
    public function addVersion(PrescriptionVersion $version): static
    {
        if (!$this->versions->contains($version)) {
            $this->versions->add($version);
            $version->setPrescription($this);
        }
        return $this;
    }

    /**
     * Retire une version de l'historique.
     */
    public function removeVersion(PrescriptionVersion $version): static
    {
        if ($this->versions->removeElement($version)) {
            if ($version->getPrescription() === $this) {
                $version->setPrescription(null);
            }
        }
        return $this;
    }
}
