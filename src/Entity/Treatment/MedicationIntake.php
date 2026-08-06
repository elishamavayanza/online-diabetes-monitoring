<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medication_intakes')]
class MedicationIntake extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: PrescriptionItem::class, inversedBy: 'intakes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?PrescriptionItem $prescriptionItem = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $takenAt = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private ?string $quantityTaken = null;

    #[ORM\Column(type: 'string', length: 50, enumType: IntakeStatus::class)]
    private ?IntakeStatus $status = null;

    public function getPrescriptionItem(): ?PrescriptionItem
    {
        return $this->prescriptionItem;
    }

    public function setPrescriptionItem(?PrescriptionItem $prescriptionItem): self
    {
        $this->prescriptionItem = $prescriptionItem;
        return $this;
    }

    public function getTakenAt(): ?\DateTimeImmutable
    {
        return $this->takenAt;
    }

    public function setTakenAt(\DateTimeImmutable $takenAt): self
    {
        $this->takenAt = $takenAt;
        return $this;
    }

    public function getQuantityTaken(): ?string
    {
        return $this->quantityTaken;
    }

    public function setQuantityTaken(string $quantityTaken): self
    {
        $this->quantityTaken = $quantityTaken;
        return $this;
    }

    public function getStatus(): ?IntakeStatus
    {
        return $this->status;
    }

    public function setStatus(IntakeStatus $status): self
    {
        $this->status = $status;
        return $this;
    }
}
