<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\HealthcareProfessional;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente l'historique des versions d'une prescription médicale.
 */
#[ORM\Entity]
#[ORM\Table(name: 'treatment_prescription_versions')]
class PrescriptionVersion extends BaseEntity
{
    /**
     * @var Prescription|null La prescription associée à cette version.
     */
    #[ORM\ManyToOne(targetEntity: Prescription::class, inversedBy: 'versions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Prescription $prescription = null;

    /**
     * @var int|null Le numéro de version.
     */
    #[ORM\Column(type: 'integer')]
    private ?int $versionNumber = null;

    /**
     * @var string|null Un résumé des modifications apportées dans cette version.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $changesSummary = null;

    /**
     * @var array Les données stockées sous forme de JSON pour cette version.
     */
    #[ORM\Column(type: 'json')]
    private array $data = [];

    /**
     * @var HealthcareProfessional|null Le professionnel de santé ayant effectué la modification.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?HealthcareProfessional $modifiedBy = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure de la modification.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $modifiedAt = null;

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
     * Récupère le numéro de version.
     */
    public function getVersionNumber(): ?int
    {
        return $this->versionNumber;
    }

    /**
     * Définit le numéro de version.
     */
    public function setVersionNumber(int $versionNumber): static
    {
        $this->versionNumber = $versionNumber;
        return $this;
    }

    /**
     * Récupère le résumé des modifications.
     */
    public function getChangesSummary(): ?string
    {
        return $this->changesSummary;
    }

    /**
     * Définit le résumé des modifications.
     */
    public function setChangesSummary(?string $changesSummary): static
    {
        $this->changesSummary = $changesSummary;
        return $this;
    }

    /**
     * Récupère les données JSON.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Définit les données JSON.
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Récupère l'auteur de la modification.
     */
    public function getModifiedBy(): ?HealthcareProfessional
    {
        return $this->modifiedBy;
    }

    /**
     * Définit l'auteur de la modification.
     */
    public function setModifiedBy(HealthcareProfessional $modifiedBy): static
    {
        $this->modifiedBy = $modifiedBy;
        return $this;
    }

    /**
     * Récupère la date de la modification.
     */
    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    /**
     * Définit la date de la modification.
     */
    public function setModifiedAt(\DateTimeImmutable $modifiedAt): static
    {
        $this->modifiedAt = $modifiedAt;
        return $this;
    }
}
