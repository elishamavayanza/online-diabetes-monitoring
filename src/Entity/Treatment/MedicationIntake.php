<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\PatientCommonOperation;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente la prise effective d'un médicament dans le cadre d'une prescription.
 */
#[ORM\Entity]
#[ORM\Table(name: 'treatment_medication_intakes')]
class MedicationIntake extends PatientCommonOperation
{
    /**
     * @var PrescriptionItem|null L'élément de prescription associé à cette prise.
     */
    #[ORM\ManyToOne(targetEntity: PrescriptionItem::class, inversedBy: 'intakes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?PrescriptionItem $prescriptionItem = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure de la prise effective.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $takenAt = null;

    /**
     * @var string|null La quantité effectivement prise.
     */
    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private ?string $quantityTaken = null;

    /**
     * @var IntakeStatus|null Le statut de la prise (pris, sauté, retardé).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: IntakeStatus::class)]
    private ?IntakeStatus $status = null;

    /**
     * Récupère l'élément de prescription associé.
     */
    public function getPrescriptionItem(): ?PrescriptionItem
    {
        return $this->prescriptionItem;
    }

    /**
     * Définit l'élément de prescription associé.
     */
    public function setPrescriptionItem(?PrescriptionItem $prescriptionItem): static
    {
        $this->prescriptionItem = $prescriptionItem;
        return $this;
    }

    /**
     * Récupère la date et l'heure de la prise.
     */
    public function getTakenAt(): ?\DateTimeImmutable
    {
        return $this->takenAt;
    }

    /**
     * Définit la date et l'heure de la prise.
     */
    public function setTakenAt(\DateTimeImmutable $takenAt): static
    {
        $this->takenAt = $takenAt;
        return $this;
    }

    /**
     * Récupère la quantité prise.
     */
    public function getQuantityTaken(): ?string
    {
        return $this->quantityTaken;
    }

    /**
     * Définit la quantité prise.
     */
    public function setQuantityTaken(string $quantityTaken): static
    {
        $this->quantityTaken = $quantityTaken;
        return $this;
    }

    /**
     * Récupère le statut de la prise.
     */
    public function getStatus(): ?IntakeStatus
    {
        return $this->status;
    }

    /**
     * Définit le statut de la prise.
     */
    public function setStatus(IntakeStatus $status): static
    {
        $this->status = $status;
        return $this;
    }
}
