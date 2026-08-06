<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prescription_items')]
class PrescriptionItem extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Prescription::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Prescription $prescription = null;

    #[ORM\ManyToOne(targetEntity: Medication::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Medication $medication = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $dosage = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private ?string $quantity = null;

    #[ORM\Column(type: 'boolean')]
    private bool $morning = false;

    #[ORM\Column(type: 'boolean')]
    private bool $noon = false;

    #[ORM\Column(type: 'boolean')]
    private bool $evening = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $instructions = null;

    #[ORM\OneToMany(mappedBy: 'prescriptionItem', targetEntity: MedicationIntake::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $intakes;

    public function __construct()
    {
        parent::__construct();
        $this->intakes = new ArrayCollection();
    }

    public function getPrescription(): ?Prescription
    {
        return $this->prescription;
    }

    public function setPrescription(?Prescription $prescription): self
    {
        $this->prescription = $prescription;
        return $this;
    }

    public function getMedication(): ?Medication
    {
        return $this->medication;
    }

    public function setMedication(?Medication $medication): self
    {
        $this->medication = $medication;
        return $this;
    }

    public function getDosage(): ?string
    {
        return $this->dosage;
    }

    public function setDosage(string $dosage): self
    {
        $this->dosage = $dosage;
        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function isMorning(): bool
    {
        return $this->morning;
    }

    public function setMorning(bool $morning): self
    {
        $this->morning = $morning;
        return $this;
    }

    public function isNoon(): bool
    {
        return $this->noon;
    }

    public function setNoon(bool $noon): self
    {
        $this->noon = $noon;
        return $this;
    }

    public function isEvening(): bool
    {
        return $this->evening;
    }

    public function setEvening(bool $evening): self
    {
        $this->evening = $evening;
        return $this;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(?string $instructions): self
    {
        $this->instructions = $instructions;
        return $this;
    }

    /**
     * @return Collection<int, MedicationIntake>
     */
    public function getIntakes(): Collection
    {
        return $this->intakes;
    }

    public function addIntake(MedicationIntake $intake): self
    {
        if (!$this->intakes->contains($intake)) {
            $this->intakes->add($intake);
            $intake->setPrescriptionItem($this);
        }
        return $this;
    }

    public function removeIntake(MedicationIntake $intake): self
    {
        if ($this->intakes->removeElement($intake)) {
            if ($intake->getPrescriptionItem() === $this) {
                $intake->setPrescriptionItem(null);
            }
        }
        return $this;
    }
}
