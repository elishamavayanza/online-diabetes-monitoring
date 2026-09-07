<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente les informations spécifiques propres à une insuline.
 */
#[ORM\Entity]
#[ORM\Table(name: 'treatment_insulins')]
class Insulin extends BaseEntity
{
    /**
     * @var Medication|null Le médicament de base représentant l'insuline.
     */
    #[ORM\ManyToOne(targetEntity: Medication::class, inversedBy: 'insulins')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Medication $medication = null;

    /**
     * @var InsulinType|null Le type d'insuline (rapide, longue, etc.).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: InsulinType::class)]
    private ?InsulinType $insulinType = null;

    /**
     * @var string|null La concentration de l'insuline (ex: U-100, U-200).
     */
    #[ORM\Column(type: 'string', length: 50)]
    private ?string $concentration = null;

    /**
     * Récupère le médicament associé.
     */
    public function getMedication(): ?Medication
    {
        return $this->medication;
    }

    /**
     * Définit le médicament associé.
     */
    public function setMedication(?Medication $medication): static
    {
        $this->medication = $medication;
        return $this;
    }

    /**
     * Récupère le type d'insuline.
     */
    public function getInsulinType(): ?InsulinType
    {
        return $this->insulinType;
    }

    /**
     * Définit le type d'insuline.
     */
    public function setInsulinType(InsulinType $insulinType): static
    {
        $this->insulinType = $insulinType;
        return $this;
    }

    /**
     * Récupère la concentration de l'insuline.
     */
    public function getConcentration(): ?string
    {
        return $this->concentration;
    }

    /**
     * Définit la concentration de l'insuline.
     */
    public function setConcentration(string $concentration): static
    {
        $this->concentration = $concentration;
        return $this;
    }
}
