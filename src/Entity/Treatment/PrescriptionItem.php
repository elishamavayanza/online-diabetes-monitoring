<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un élément individuel d'une prescription (un médicament et sa posologie).
 */
#[ORM\Entity]
#[ORM\Table(name: 'treatment_prescription_items')]
class PrescriptionItem extends BaseEntity
{
    /**
     * @var Prescription|null La prescription à laquelle appartient cet élément.
     */
    #[ORM\ManyToOne(targetEntity: Prescription::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Prescription $prescription = null;

    /**
     * @var Medication|null Le médicament prescrit.
     */
    #[ORM\ManyToOne(targetEntity: Medication::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Medication $medication = null;

    /**
     * @var string|null La posologie ou le dosage du médicament.
     */
    #[ORM\Column(type: 'string', length: 100)]
    private ?string $dosage = null;

    /**
     * @var string|null La quantité prescrite.
     */
    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private ?string $quantity = null;

    /**
     * @var bool Indique si le médicament doit être pris le matin.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $morning = false;

    /**
     * @var bool Indique si le médicament doit être pris le midi.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $noon = false;

    /**
     * @var bool Indique si le médicament doit être pris le soir.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $evening = false;

    /**
     * @var string|null Instructions particulières pour la prise.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $instructions = null;

    /**
     * @var Collection<int, MedicationIntake> La collection des prises effectives associées.
     */
    #[ORM\OneToMany(mappedBy: 'prescriptionItem', targetEntity: MedicationIntake::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $intakes;

    /**
     * Constructeur pour initialiser la collection des prises.
     */
    public function __construct()
    {
        $this->intakes = new ArrayCollection();
    }

    /**
     * Récupère la prescription associée.
     */
    public function getPrescription(): ?Prescription
    {
        return $this->prescription;
    }

    /**
     * Définit la prescription associée.
     */
    public function setPrescription(?Prescription $prescription): static
    {
        $this->prescription = $prescription;
        return $this;
    }

    /**
     * Récupère le médicament.
     */
    public function getMedication(): ?Medication
    {
        return $this->medication;
    }

    /**
     * Définit le médicament.
     */
    public function setMedication(?Medication $medication): static
    {
        $this->medication = $medication;
        return $this;
    }

    /**
     * Récupère le dosage.
     */
    public function getDosage(): ?string
    {
        return $this->dosage;
    }

    /**
     * Définit le dosage.
     */
    public function setDosage(string $dosage): static
    {
        $this->dosage = $dosage;
        return $this;
    }

    /**
     * Récupère la quantité.
     */
    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    /**
     * Définit la quantité.
     */
    public function setQuantity(string $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Indique si la prise du matin est activée.
     */
    public function isMorning(): bool
    {
        return $this->morning;
    }

    /**
     * Définit si la prise du matin est activée.
     */
    public function setMorning(bool $morning): static
    {
        $this->morning = $morning;
        return $this;
    }

    /**
     * Indique si la prise du midi est activée.
     */
    public function isNoon(): bool
    {
        return $this->noon;
    }

    /**
     * Définit si la prise du midi est activée.
     */
    public function setNoon(bool $noon): static
    {
        $this->noon = $noon;
        return $this;
    }

    /**
     * Indique si la prise du soir est activée.
     */
    public function isEvening(): bool
    {
        return $this->evening;
    }

    /**
     * Définit si la prise du soir est activée.
     */
    public function setEvening(bool $evening): static
    {
        $this->evening = $evening;
        return $this;
    }

    /**
     * Récupère les instructions.
     */
    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    /**
     * Définit les instructions.
     */
    public function setInstructions(?string $instructions): static
    {
        $this->instructions = $instructions;
        return $this;
    }

    /**
     * Récupère la collection des prises.
     *
     * @return Collection<int, MedicationIntake>
     */
    public function getIntakes(): Collection
    {
        return $this->intakes;
    }

    /**
     * Ajoute une prise à l'élément de prescription.
     */
    public function addIntake(MedicationIntake $intake): static
    {
        if (!$this->intakes->contains($intake)) {
            $this->intakes->add($intake);
            $intake->setPrescriptionItem($this);
        }
        return $this;
    }

    /**
     * Retire une prise de l'élément de prescription.
     */
    public function removeIntake(MedicationIntake $intake): static
    {
        if ($this->intakes->removeElement($intake)) {
            if ($intake->getPrescriptionItem() === $this) {
                $intake->setPrescriptionItem(null);
            }
        }
        return $this;
    }
}
