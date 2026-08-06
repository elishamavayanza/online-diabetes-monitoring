<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\HealthcareProfessional;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prescription_versions')]
class PrescriptionVersion extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Prescription::class, inversedBy: 'versions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Prescription $prescription = null;

    #[ORM\Column(type: 'integer')]
    private ?int $versionNumber = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $changesSummary = null;

    #[ORM\Column(type: 'json')]
    private array $data = [];

    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?HealthcareProfessional $modifiedBy = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $modifiedAt = null;

    public function getPrescription(): ?Prescription
    {
        return $this->prescription;
    }

    public function setPrescription(?Prescription $prescription): self
    {
        $this->prescription = $prescription;
        return $this;
    }

    public function getVersionNumber(): ?int
    {
        return $this->versionNumber;
    }

    public function setVersionNumber(int $versionNumber): self
    {
        $this->versionNumber = $versionNumber;
        return $this;
    }

    public function getChangesSummary(): ?string
    {
        return $this->changesSummary;
    }

    public function setChangesSummary(?string $changesSummary): self
    {
        $this->changesSummary = $changesSummary;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getModifiedBy(): ?HealthcareProfessional
    {
        return $this->modifiedBy;
    }

    public function setModifiedBy(HealthcareProfessional $modifiedBy): self
    {
        $this->modifiedBy = $modifiedBy;
        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(\DateTimeImmutable $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;
        return $this;
    }
}
